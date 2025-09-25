<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateTimeHelper;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\DateComment;
use App\Models\DateDisabled;
use App\Repositories\AppointmentRepository;
use App\Services\BlacklistService;
use App\Services\CalendarService;
use App\Services\FileService;
use App\Services\VaccineService;
use DateTime;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentRepository $appointmentRepository, protected BlacklistService $blacklistService, protected FileService $fileService, protected VaccineService $vaccineService, protected CalendarService $calendarService) {}

    public function index(Request $request)
    {
        $vaccines=$this->vaccineService->allOrderedByName();
        $query=$request->query();
        $query['comment'] = $request->query('age') ? $request->query('age')."\n\n".$request->query('message') : $request->query('message');
        $query['date'] = $request->query('date') ? date('Y-m-d', strtotime($request->query('date'))) : '';

        $working_days = [1, 2, 3, 4, 5, 6];

        $weeks=$this->calendarService->getWeeks($working_days, 28);

        return view('admin.appointments.index', [
            'vaccines'     => $vaccines,
            'query'        => $query,
            'weeks'        => $weeks,
            'working_days' => $working_days,
        ]);
    }

    function filter(Request $request)
    {
        $weekdays=[
            '',
            'ПН',
            'ВТ',
            'СР',
            'ЧТ',
            'ПТ',
            'СБ',
            'НД',
        ];

        $months=[
            'січня',
            'лютого',
            'березня',
            'квітня',
            'травня',
            'червня',
            'липня',
            'серпня',
            'вересня',
            'жовтня',
            'листопада',
            'грудня',
        ];

        $start_timestamp=$end_timestamp=null;

        if ($request->input('filters.date'))
        {
            $start_timestamp=strtotime($request->input('filters.date'));
            $end_timestamp=strtotime($request->input('filters.date').' 23:59:59');
        }

        $filters=[
            'start_timestamp' => $start_timestamp,
            'end_timestamp'   => $end_timestamp,
            'tel'             => $request->input('filters.tel'),
            'name'            => $request->input('filters.name'),
            'comment'         => $request->input('filters.comment'),
            'vaccine'         => $request->input('filters.vaccine'),
        ];

        $appointments=$this->appointmentRepository->findByFilters($filters, 'date', 'DESC', $request->input('start'), $request->input('length'));

        $data=[];
        foreach ($appointments as $appointment)
        {
            if (date('Ymd')===date('Ymd', $appointment->date))
                $date='Сьогодні';
            elseif (date('Ymd', strtotime('tomorrow'))===date('Ymd', $appointment->date))
                $date='Завтра';
            else
                $date=date('d/m/y', $appointment->date).' <small>'.$weekdays[date('N', $appointment->date)].'</small>';

            $visits_to_date=0;

            if ($appointment->tel)
                $visits_to_date=Appointment::where('tel', $appointment->tel)->where('date', '<=', time())->count();

            $vaccines=$this->vaccineService->formatForApi($appointment->vaccines);

            if ($appointment->isToday())
                $date_text='сьогодні';
            elseif ($appointment->isTomorrow())
                $date_text='завтра';
            else
                $date_text=date('j', $appointment->date).' '.$months[date('n', $appointment->date)-1];

            $notify_sms_text = ($appointment->isToday() || $appointment->isTomorrow()) ? '' : sprintf("%s вiзиту Вам надiйде смс-нагадування\n\n", (date('H', $appointment->date)<12 || (date('H:i', $appointment->date)==='12:00')) ? 'За день до' : 'В день');

            $time_text=(date('H', $appointment->date)==='11' ? 'об' : 'о').' '.date('G:i', $appointment->date);

            $appointment_text=sprintf($appointment->online ? "✅ Лікар зв'яжеться з Вами в Телеграмі %s %s\n\nДякуємо, що довіряєте нам! ❤️" : "✅ Лікар чекатиме Вас %s %s\n\nНаша адреса %s\n\n%sДякуємо, що довіряєте нам! ❤️", $date_text, $time_text, config('business.address'), $notify_sms_text);

            $next_appointment=Appointment
                ::where('date', '>', $appointment->date)
                ->where('date', '<', strtotime('tomorrow', $appointment->date))
                ->orderBy('date', 'asc')
                ->first()
            ;

            $data[]=[
                'id'                 => $appointment->id,
                'name'               => trim($appointment->name),
                'tel'                => $appointment->tel,
                'comment'            => $appointment->comment,
                'comment_formatted'  => $appointment->getCommentFormattedAttribute(),
                'file'               => $appointment->file,
                'readable_date'      => $date,
                'date'               => date('Y-m-d', $appointment->date),
                'time'               => date('H:i', $appointment->date),
                'duration'           => $next_appointment ? round(($next_appointment->date-$appointment->date)/60) : null,
                'is_future'          => $appointment->date>time(),
                'is_today'           => date('Y-m-d', $appointment->date)===date('Y-m-d'),
                'is_tomorrow'        => date('Y-m-d', $appointment->date)===date('Y-m-d', strtotime('tomorrow')),
                'visits_to_date'     => $visits_to_date,
                'blacklisted'        => $appointment->tel ? $this->blacklistService->isBlacklisted($appointment->tel) : null,
                'blacklisted_reason' => $appointment->tel ? htmlentities($this->blacklistService->getBlacklistReason($appointment->tel)) : null,
                'vaccines'           => $vaccines,
                'neurology'          => $appointment->neurology,
                'earlier'            => $appointment->earlier,
                'call_back'          => $appointment->call_back,
                'online'             => $appointment->online,
                'created_at'         => DateTimeHelper::formatTimestamp($appointment->created_at),
                'updated_at'         => DateTimeHelper::formatTimestamp($appointment->updated_at),
                'appointment_text'   => $appointment_text,
            ];
        }

        $dates=$this->calendarService->getDatesRange(28, 6);

        $data_by_dates=[];
        foreach ($dates as $date)
        {
            $appointments=$this->appointmentRepository->findByFilters(['start_timestamp' => $date->getTimestamp(), 'end_timestamp' => $date->getTimestamp()+24*60*60,
            ]);

            $vaccines_count=0;
            foreach ($appointments as $appointment)
                $vaccines_count+=$appointment->vaccines->count();

            $data_by_dates[$date->format('Y-m-d')]=[
                'appointments_count' => $appointments->count(),
                'vaccines_count'     => $vaccines_count,
                'is_disabled'        => DateDisabled::where('date', $date)->exists(),
                'comment'            => DateComment::where('date', $date)?->value('comment'),
                'is_future'          => $date >= new DateTime('today'),
            ];
        }

        $tel=trim($filters['tel']);

        $blacklisted=[];
        if ($tel)
        {
            $blacklist=$this->blacklistService->search(trim($filters['tel']));

            foreach ($blacklist as $item)
                $blacklisted[$item->tel]=$item->reason_and_name;
        }

        die(json_encode([
            'data'            => $data,
            'recordsFiltered' => $this->appointmentRepository->findByFilters($filters)->count(),
            'recordsTotal'    => Appointment::count(),
            'dates'           => $data_by_dates,
            'blacklist'       => $blacklisted,
        ]));
    }

    public function delete(Appointment $appointment)
    {
        $this->fileService->delete($appointment->file);
        $appointment->delete();

        return response()->json(['success' => true]);
    }

    public function file(Appointment $appointment)
    {
        if (!$appointment->file || !$this->fileService->exists($appointment->file))
            abort(404, 'Файл не знайдено');

        return response()->download($this->fileService->getPath($appointment->file), basename($appointment->file));
    }

    public function files()
    {
        $appointments_with_file=Appointment
            ::whereNotNull('file')
            ->orderBy('file', 'asc')
            ->get()
        ;

        return view('admin.appointments.files', [
            'appointments' => $appointments_with_file,
        ]);
    }

    public function fileUpload(Appointment $appointment, Request $request)
    {
        $request->validate([
            'file_uploader' => 'required|mimes:doc,docx,pdf',
        ]);

        $this->fileService->delete($appointment->file);

        $filename=$this->fileService->upload($request->file('file_uploader'));

        $appointment->timestamps=false;
        $appointment->file=$filename;
        $appointment->save();

        return response()->json(['error'=>null]);
    }

    public function graph()
    {
        return view('admin.appointments.graph');
    }

    public function graphData()
    {
        $appointments=Appointment
            ::where('date', '>', strtotime('2021-01-04'))
            ->orderBy('date', 'asc')
            ->get()
        ;

        $appointments_by_date=[];
        foreach ($appointments as $appointment)
        {
            $date=date('Y-m-d', $appointment->date);
            $appointments_by_date[$date]=($appointments_by_date[$date] ?? 0)+1;
        }

        return response()->json($appointments_by_date);
    }

    public function history(Request $request)
    {
        $request->validate([
            'tel' => 'required|string',
        ]);

        $appointments=Appointment
            ::where('tel', $request->input('tel'))
            ->where('date', '<', time())
            ->orderBy('date', 'DESC')
            ->get()
        ;

        $data=$appointments->map(function ($appointment) {
            $vaccines=$appointment->vaccines->map(function ($vaccine) {
                return [
                    'id'         => $vaccine->id,
                    'name'       => $vaccine->name,
                    'short_name' => $vaccine->short_name,
                ];
            });

            return [
                'id'            => $appointment->id,
                'date'          => date('Y-m-d', $appointment->date)===date('Y-m-d') ? 'Сьогодні' : date('d/m/y', $appointment->date),
                'days_ago'      => DateTimeHelper::daysAgo($appointment->date),
                'address_label' => $appointment->date<strtotime('2023-02-14') ? 'Кобилиці' : '',
                'name'          => $appointment->name,
                'comment'       => $appointment->comment_formatted,
                'file'          => $appointment->file,
                'vaccines'      => $vaccines,
                'neurology'     => $appointment->neurology,
                'earlier'       => $appointment->earlier,
                'online'        => $appointment->online,
                'call_back'     => $appointment->call_back,
            ];
        });

        return response()->json($data);
    }

    public function getByTelephone(Request $request)
    {
        $request->validate([
            'tel' => 'required|string',
        ]);

        $tel=StringHelper::normalizeTelephone($request->input('tel'));

        $name=Appointment
            ::where('tel', $tel)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderByDesc('date')
            ->value('name')
        ;

        return response()->json([
            'name'               => $name,
            'blacklisted'        => $this->blacklistService->isBlacklisted($tel),
            'blacklisted_reason' => $this->blacklistService->getBlacklistReason($tel),
        ]);
    }

    public function save(AppointmentRequest $request, Appointment $appointment = null)
    {
        $appointment ??= new Appointment;

        $old_date = $appointment->date ?? null;

        $appointment->fill([
            'name'       => $request->name ?? '',
            'tel'        => $request->input('tel'),
            'date'       => strtotime($request->date.' '.$request->time),
            'comment'    => $request->comment ?? '',
            'neurology'  => $request->boolean('neurology'),
            'earlier'    => $request->boolean('earlier'),
            'online'     => $request->boolean('online'),
            'call_back'  => $request->boolean('call_back'),
        ]);

        if ($old_date && $old_date!==$appointment->date)
            $appointment->sms_notified=0;

        $appointment->save();

        $this->vaccineService->syncAppointmentVaccines($appointment, $request->input('vaccines', []));

        return response()->json(['success' => true]);
    }
}
