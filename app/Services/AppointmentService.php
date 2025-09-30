<?php

namespace App\Services;

use App\Helpers\DateTimeHelper;
use App\Helpers\StringHelper;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use App\Models\DateComment;
use App\Models\DateDisabled;
use DateTime;
use Illuminate\Http\UploadedFile;

class AppointmentService
{
    public function __construct(
        protected AppointmentRepository $appointmentRepository,
        protected VaccineService $vaccineService,
        protected BlacklistService $blacklistService,
        protected CalendarService $calendarService,
        protected FileService $fileService,
    ) {}

    public function save(AppointmentRequest $request, ?Appointment $appointment=null): Appointment
    {
        $appointment ??= new Appointment;

        $old_date = $appointment->date ?? null;

        $appointment->fill([
            'name'      => $request->name ?? '',
            'tel'       => StringHelper::normalizeTelephone($request->input('tel')),
            'date'      => strtotime($request->date.' '.$request->time),
            'comment'   => $request->comment ?? '',
            'neurology' => $request->boolean('neurology'),
            'earlier'   => $request->boolean('earlier'),
            'online'    => $request->boolean('online'),
            'call_back' => $request->boolean('call_back'),
        ]);

        if ($old_date && $old_date!==$appointment->date)
            $appointment->sms_notified=0;

        $appointment->save();

        $this->vaccineService->syncAppointmentVaccines($appointment, $request->input('vaccines', []));

        return $appointment;
    }

    public function getHistoryByTelephone(string $tel): array
    {
        $appointments=$this->appointmentRepository->findPastByTelephone($tel);

        return $appointments->map(function ($appointment) {
            $appointmentDate=Carbon::createFromTimestamp($appointment->date)->locale('uk');

            $vaccines = $appointment->vaccines->map(fn($vaccine) => [
                'id'         => $vaccine->id,
                'name'       => $vaccine->name,
                'short_name' => $vaccine->short_name,
            ]);

            return [
                'id'            => $appointment->id,
                'date'          => $appointmentDate->isToday() ? 'Сьогодні' : $appointmentDate->isoFormat('DD/MM/YY'),
                'days_ago'      => DateTimeHelper::daysAgo($appointment->date),
                'address_label' => $appointmentDate->lt(Carbon::create(2023, 2, 14)) ? 'Кобилиці' : '',
                'name'          => $appointment->name,
                'comment'       => $appointment->comment_formatted,
                'file'          => $appointment->file,
                'vaccines'      => $vaccines,
                'neurology'     => $appointment->neurology,
                'earlier'       => $appointment->earlier,
                'online'        => $appointment->online,
                'call_back'     => $appointment->call_back,
            ];
        })->toArray();
    }

    public function getByTelephone(string $tel): array
    {
        $tel=StringHelper::normalizeTelephone($tel);

        $name=$this->appointmentRepository->getLastNameByTelephone($tel);

        return [
            'name'               => $name,
            'blacklisted'        => $this->blacklistService->isBlacklisted($tel),
            'blacklisted_reason' => $this->blacklistService->getBlacklistReason($tel),
        ];
    }

    public function getGraphData(): array
    {
        $appointments=$this->appointmentRepository->getGraphData();

        $appointments_by_date=[];
        foreach ($appointments as $appointment)
        {
            $date=Carbon::createFromTimestamp($appointment->date)->toDateString();
            $appointments_by_date[$date]=($appointments_by_date[$date] ?? 0)+1;
        }

        return $appointments_by_date;
    }

    private function buildAppointmentMessage(Appointment $appointment): string
    {
        $dt=Carbon::createFromTimestamp($appointment->date);

        $date_text = $dt->isToday() ? 'сьогодні' : ($dt->isTomorrow() ? 'завтра' : Carbon::createFromTimestamp($appointment->date)->locale('uk')->isoFormat('D MMMM'));

        $notify_sms_text = ($dt->isToday() || $dt->isTomorrow()) ? '' : sprintf("%s вiзиту Вам надiйде смс-нагадування\n\n", ($dt->hour<12 || $dt->format('H:i')==='12:00') ? 'За день до' : 'В день');

        $time_text = ($dt->hour === 11 ? 'об' : 'о').' '.$dt->format('G:i');

        return sprintf($appointment->online ? "✅ Лікар зв'яжеться з Вами в Телеграмі %s %s\n\nДякуємо, що довіряєте нам! ❤️" : "✅ Лікар чекатиме Вас %s %s\n\nНаша адреса %s\n\n%sДякуємо, що довіряєте нам! ❤️", $date_text, $time_text, config('business.address'), $notify_sms_text);
    }

    public function filter(array $input): array
    {
        $start_timestamp=$end_timestamp=null;

        if (!empty($input['filters']['date']))
        {
            $start_timestamp=strtotime($input['filters']['date']);
            $end_timestamp=strtotime($input['filters']['date'].' 23:59:59');
        }

        $filters=[
            'start_timestamp' => $start_timestamp,
            'end_timestamp'   => $end_timestamp,
            'tel'             => $input['filters']['tel'] ?? null,
            'name'            => $input['filters']['name'] ?? null,
            'comment'         => $input['filters']['comment'] ?? null,
            'vaccine'         => $input['filters']['vaccine'] ?? null,
        ];

        $appointments=$this->appointmentRepository->findByFilters($filters, 'date', 'DESC', $input['start'] ?? null, $input['length'] ?? null);

        $data=[];
        foreach ($appointments as $appointment)
        {
            $dt=Carbon::createFromTimestamp($appointment->date)->locale('uk');

            $date = match (true) {
                $dt->isToday()    => 'Сьогодні',
                $dt->isTomorrow() => 'Завтра',
                default           => $dt->isoFormat('DD/MM/YY').' <small>'.strtoupper($dt->isoFormat('dd')).'</small>',
            };

            $visits_to_date = $appointment->tel ? Appointment::where('tel', $appointment->tel)->where('date', '<=', time())->count() : 0;

            $vaccines=$this->vaccineService->formatForApi($appointment->vaccines);

            $next_appointment=$this->appointmentRepository->getNextAppointment($appointment->date);

            $dt=Carbon::createFromTimestamp($appointment->date);

            $data[]=[
                'id'                 => $appointment->id,
                'name'               => trim($appointment->name),
                'tel'                => $appointment->tel,
                'comment'            => $appointment->comment,
                'comment_formatted'  => $appointment->getCommentFormattedAttribute(),
                'file'               => $appointment->file,
                'readable_date'      => $date,
                'date'               => $dt->toDateString(),
                'time'               => $dt->format('H:i'),
                'duration'           => $next_appointment ? round(($next_appointment->date - $appointment->date) / 60) : null,
                'is_future'          => $dt->isFuture(),
                'is_today'           => $dt->isToday(),
                'is_tomorrow'        => $dt->isTomorrow(),
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
                'appointment_text'   => $this->buildAppointmentMessage($appointment),
            ];
        }

        $dates=$this->calendarService->getDatesRange(28, 6);
        $data_by_dates=[];
        foreach ($dates as $date)
        {
            $appointments=$this->appointmentRepository->findByFilters([
                'start_timestamp'=>$date->getTimestamp(),
                'end_timestamp'  =>$date->getTimestamp()+24*60*60,
            ]);

            $vaccines_count=$appointments->sum(fn($a) => $a->vaccines->count());

            $data_by_dates[$date->format('Y-m-d')]=[
                'appointments_count' => $appointments->count(),
                'vaccines_count'     => $vaccines_count,
                'is_disabled'        => DateDisabled::where('date', $date)->exists(),
                'comment'            => DateComment::where('date', $date)?->value('comment'),
                'is_future'          => $date >= new DateTime('today'),
            ];
        }

        $blacklisted=[];
        if (!empty($filters['tel']))
        {
            $blacklist=$this->blacklistService->search(trim($filters['tel']));
            foreach ($blacklist as $item)
                $blacklisted[$item->tel]=$item->reason_and_name;
        }

        return [
            'data'            => $data,
            'recordsFiltered' => $this->appointmentRepository->findByFilters($filters)->count(),
            'recordsTotal'    => Appointment::count(),
            'dates'           => $data_by_dates,
            'blacklist'       => $blacklisted,
        ];
    }

    public function delete(Appointment $appointment): void
    {
        $this->fileService->delete($appointment->file);
        $appointment->delete();
    }

    public function uploadFile(Appointment $appointment, UploadedFile $file): void
    {
        if ($appointment->file)
            app(FileService::class)->delete($appointment->file);

        $filename=app(FileService::class)->upload($file);

        $appointment->timestamps=false;
        $appointment->file=$filename;

        $appointment->saveQuietly();
    }

    public function getAppointmentsWithFiles()
    {
        return $this->appointmentRepository->getAppointmentsWithFiles();
    }
}
