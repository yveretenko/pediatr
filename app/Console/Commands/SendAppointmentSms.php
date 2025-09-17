<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentSms extends Command
{
    protected $signature='sms:send-appointment-sms';
    protected $description='Send SMS to upcoming appointments';

    public function handle()
    {
        $log=[];

        $hour=(int)now()->format('G');
        if ($hour<9)
            $log[]='Ранувато будити людей';
        else
        {
            $service = new SmsService('', '', config('sms.key'));

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

            foreach ($appointments as $appointment)
            {
                $sms_text=sprintf(
                    'Чекаємо Вас%s о%s %s в кабінеті педіатра ДітиКвіти, %s',
                    $appointment->isTomorrow() ? ' завтра' : '',
                    date('G', $appointment->date)=='11' ? 'б' : '',
                    date('G:i', $appointment->date),
                    config('business.address')
                );

                $log[]=sprintf("Відправляємо смс на номер `%s` з текстом: `%s`", $appointment->tel, $sms_text);

                if (env('SEND_SMS', false))
                {
                    $service->sendSMS('DitiKviti', $appointment->tel, $sms_text);

                    if ($service->hasErrors())
                    {
                        Mail::html("СМС на номер `".$appointment->tel."` з текстом: <blockquote>$sms_text</blockquote>Помилки:<blockquote>".implode('<br><br>', $service->getErrors())."</blockquote>", function($message){
                            $message
                                ->to('yura11v@gmail.com')
                                ->subject('ДітиКвіти - помилка при відправці смс')
                            ;
                        });

                        $log[]="Помилки при відправці смс: ".implode("\n\n", $service->getErrors());
                    }
                }

                $appointment->sms_notified=1;

                $appointment->save();
            }
        }

        Log::info(implode(PHP_EOL, $log));
    }
}
