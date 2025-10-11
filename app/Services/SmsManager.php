<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class SmsManager
{
    public SmsService $service;

    public function __construct()
    {
        $this->service = new SmsService('', '', config('sms.key'));
    }

    public function send($to, $text): bool
    {
        $this->service->sendSms(config('sms.sender'), $to, $text);

        if ($this->service->hasErrors())
        {
            $errors=implode('<br><br>', $this->service->getErrors());

            Mail::html(
                "СМС на номер `$to` з текстом: <blockquote>$text</blockquote>Помилки:<blockquote>$errors</blockquote>",
                function ($message) {
                    $message
                        ->to('yura11v@gmail.com')
                        ->subject('ДітиКвіти - помилка при відправці смс')
                    ;
                }
            );

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->service->getErrors();
    }
}
