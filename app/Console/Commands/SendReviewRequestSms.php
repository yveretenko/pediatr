<?php

namespace App\Console\Commands;

use App\Services\SmsManager;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class SendReviewRequestSms extends Command
{
    protected $signature='sms:send-review-request-sms';
    protected $description='Send review request SMS to patients after their third visit';

    public function handle(): void
    {
        $log=[];

        $appointments=Appointment::thirdVisitsYesterday(); // TODO: Implement this method in the Appointment model

        $log[]='Знайдено телефонів для відправки смс: '.count($appointments);

        $sms_manager = new SmsManager;

        foreach ($appointments as $appointment)
        {
            $sms_text=sprintf(
                "Нещодавно ви відвідали кабінет дитячого лікаря ДітиКвіти по %s\n\nМи стараємося і цінуємо вашу думку. Поділіться своїми враженнями: https://g.page/r/CSGLSyAY-HosEAg/review\n\nДякуємо",
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
        }

        Log::channel('cron')->info(implode(PHP_EOL, $log));
    }
}
