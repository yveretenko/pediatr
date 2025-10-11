<?php

namespace App\Services;

use App\Events\SmsFailed;

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
            event(new SmsFailed($to, $text, $this->service->getErrors()));

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->service->getErrors();
    }
}
