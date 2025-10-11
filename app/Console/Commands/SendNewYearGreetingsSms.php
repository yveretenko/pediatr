<?php

namespace App\Console\Commands;

use App\Services\SmsManager;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class SendNewYearGreetingsSms extends Command
{
    protected $signature='sms:send-new-year-greetings-sms';
    protected $description='Send New Year SMS to patients that had 5 or more appointments this year';

    public function handle(): void
    {
        $log=[];

        $appointments=Appointment
            ::query()
            ->select('tel')
            ->selectRaw('COUNT(id) as total')
            ->whereNotNull('tel')
            ->where('date', '>=', strtotime('first day of January this year'))
            ->groupBy('tel')
            ->havingRaw('COUNT(id) >= 5')
            ->get()
        ;

        $log[]="Знайдено телефонів для відправки смс: ".$appointments->count();

        $sms_manager = new SmsManager;

        $sent=0;
        foreach ($appointments as $appointment)
        {
            $sms_text=sprintf('Кабінет дитячого лікаря ДітиКвіти щиро вітає Вас з Новим %d роком. Бажаємо міцного здоров\'я Вашій родині та лише профілактичних оглядів. Дякуємо за довіру!', (int)date('Y')+1);

            if (env('SEND_SMS', false))
            {
                if (!$sms_manager->send($appointment->tel, $sms_text))
                    $log[]="Помилки при відправці смс: ".implode("\n\n", $sms_manager->getErrors());
            }
            else
            {
                $log[]='СМС не відправлено, тому що в налаштуваннях вимкнено відправку смс';
                $sent++;
            }
        }

        $log[]="Відправлено $sent смс";

        Log::channel('cron')->info(implode(PHP_EOL, $log));
    }
}
