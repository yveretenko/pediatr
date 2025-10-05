<?php

namespace App\Http\Controllers\Application;

use App\Helpers\StringHelper;
use App\Models\Blacklist;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class AppointmentRequestController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:50',
            'message' => 'required|string',
            'date'    => 'nullable|date',
            'age'     => 'nullable|string|max:50',
        ]);

        $name=strip_tags($request->input('name'));
        $tel=strip_tags($request->input('phone'));
        $message=strip_tags($request->input('message'));
        $date=$request->input('date');
        $age=$request->input('age');

        $normalized_tel=StringHelper::normalizeTelephone($tel);

        $visits_count=Appointment::where('tel', $normalized_tel)->count();

        $convert_link=route('admin.appointments', $request->all());
        $convert_button='<a href="'.$convert_link.'">Конвертувати в запис</a>';

        $blacklist_record=Blacklist::find($normalized_tel);

        $weekdays=['', 'понеділок', 'вівторок', 'середа', 'четвер', 'п\'ятниця', 'субота', 'неділя'];

        $body=[
            "Ім'я дитини: $name",
            "Вік дитини: $age",
            "Телефон: <a href='tel:$normalized_tel'>$tel</a> (".
            ($visits_count
                ? "за цим номером знайдено <a href='".route('admin.appointments', ['tel'=>$normalized_tel])."'>$visits_count запис".($visits_count>1 ? ($visits_count<5 ? 'и' : 'ів') : '')."</a>"
                : 'за цим номером не знайдено записів'
            ).")",
            $blacklist_record ? 'Телефон в чорному списку, причина: '.($blacklist_record->reason_and_name ?: '-') : null,
            $date ? "Бажана дата: $date (".$weekdays[date('N', strtotime($date))].")" : null,
            "Причина звернення:",
            nl2br($message),
            $convert_button,
        ];

        $success=true;

        try
        {
            Mail::html(implode('<br><br>', array_filter($body)), function($message) use ($blacklist_record) {
                $message
                    ->to('yura11v@gmail.com')
                    ->subject(sprintf('Запит з pediatr.cv.ua (%s)%s', strip_tags(trim($_POST['name'])), !is_null($blacklist_record) ? ' телефон в чорному списку' : ''))
                ;
            });
        }
        catch (Exception $e)
        {
            $success=false;
            $error=$e->getMessage();
        }

        if (!$success)
            return response()->json(['error' => $error ?? 'Не вдалося відправити лист'], 500);

        return response()->json(['success' => true]);
    }
}
