<?php

namespace App\Listeners;

use App\Events\SmsFailed;
use Illuminate\Support\Facades\Mail;

class SendSmsFailedNotification
{
    public function handle(SmsFailed $event): void
    {
        $errors=implode('<br><br>', $event->errors);

        Mail::html(
            sprintf('СМС на номер `%s` з текстом: <blockquote>%s</blockquote>Помилки:<blockquote>%s</blockquote>', $event->to, $event->text, $errors),
            function ($message) {
                $message
                    ->to('yura11v@gmail.com')
                    ->subject('ДітиКвіти - помилка при відправці смс')
                ;
            }
        );
    }
}
