<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewYearGreetingsSms extends Command
{
    protected $signature='sms:send-new-year-greetings-sms';
    protected $description='Send New Year SMS to patients that had 5 or more appointments this year';

    public function handle()
    {
        $log=[];

        $service = new SmsService('', '', config('sms.key'));

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

        $sent=0;
        foreach ($appointments as $appointment)
        {
            $sms_text=sprintf('Кабінет дитячого лікаря ДітиКвіти щиро вітає Вас з Новим %d роком. Бажаємо міцного здоров\'я Вашій родині та лише профілактичних оглядів. Дякуємо за довіру!', (int)date('Y')+1);

            if (config('sms.send_sms'))
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
                else
                    $sent++;
            }
        }

        $log[]="Відправлено $sent смс";

        Log::info(implode(PHP_EOL, $log));
    }
}
