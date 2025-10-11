<?php

namespace App\Console\Commands;

use App\Services\SmsManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class SendAppointmentSms extends Command
{
    protected $signature='sms:send-appointment-sms';
    protected $description='Send SMS to upcoming appointments';

    public function handle(): void
    {
        $log=[];

        $hour=(int)now()->format('G');
        if ($hour<9)
            $log[]='Ранувато будити людей';
        else
        {
            $from_time=now()->timestamp;
            $to_time=now()->addHours($hour===20 ? 16 : 6)->timestamp;

            $appointments=Appointment
                ::whereNotNull('tel')
                ->where('online', false)
                ->where('sms_notified', false)
                ->whereBetween('date', [$from_time, $to_time])
                ->get()
            ;

            $log[]="Знайдено записів на найближчі години: ".$appointments->count();

            $sms_manager = new SmsManager;

            foreach ($appointments as $appointment)
            {
                $dt=Carbon::createFromTimestamp($appointment->date);

                $sms_text=sprintf(
                    'Чекаємо Вас%s о%s %s в кабінеті педіатра ДітиКвіти, %s',
                    $dt->isTomorrow() ? ' завтра' : '',
                    $dt->hour === 11 ? 'б' : '',
                    $dt->format('G:i'),
                    config('business.address')
                );

                $log[]=sprintf("Відправляємо смс на номер `%s` з текстом: `%s`", $appointment->tel, $sms_text);

                if (env('SEND_SMS', false))
                {
                    if (!$sms_manager->send($appointment->tel, $sms_text))
                        $log[]="Помилки при відправці смс: ".implode("\n\n", $sms_manager->getErrors());
                }
                else
                    $log[]='СМС не відправлено, тому що в налаштуваннях вимкнено відправку смс';

                $appointment->sms_notified=1;
                $appointment->timestamps=false;

                $appointment->saveQuietly();
            }
        }

        Log::channel('cron')->info(implode(PHP_EOL, $log));
    }
}
