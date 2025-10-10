<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReviewRequestSms extends Command
{
    protected $signature='sms:send-review-request-sms';
    protected $description='Send review request SMS to patients after their third visit';

    public function handle(): void
    {
        $log=[];

        $service = new SmsService('', '', config('sms.key'));

        $appointments=Appointment::thirdVisitsYesterday(); // TODO: Implement this method in the Appointment model

        $log[]='Знайдено телефонів для відправки смс: '.count($appointments);

        foreach ($appointments as $appointment)
        {
            $sms_text=sprintf(
                "Нещодавно ви відвідали кабінет дитячого лікаря ДітиКвіти по %s\n\nМи стараємося і цінуємо вашу думку. Поділіться своїми враженнями: https://g.page/r/CSGLSyAY-HosEAg/review\n\nДякуємо",
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
        }

        Log::channel('cron')->info(implode(PHP_EOL, $log));
    }
}
