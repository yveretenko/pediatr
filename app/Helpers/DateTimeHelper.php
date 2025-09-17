<?php

namespace App\Helpers;

class DateTimeHelper
{
    public static function formatTimestamp(?int $timestamp): ?string
    {
        if (!$timestamp)
            return null;

        if (date('Ymd', $timestamp)===date('Ymd'))
            $date='Сьогодні';
        elseif (date('Ymd', $timestamp)===date('Ymd', strtotime('-1 day')))
            $date='Вчора';
        else
            $date=date('d/m/Y', $timestamp);

        return $date.' '.date('H:i', $timestamp);
    }

    public static function daysAgo($timestamp): string
    {
        $days_ago=(strtotime('today')-strtotime(date('Y-m-d', $timestamp)))/86400;

        $class='text=muted';

        if ($days_ago<1)
            $text='';
        elseif ($days_ago<2)
        {
            $text='вчора';
            $class='text-success font-weight-bold';
        }
        elseif ($days_ago<7)
        {
            $text='&lt;7д';
            $class='text-success font-weight-bold';
        }
        elseif ($days_ago<14)
            $text='&lt;14д';
        elseif ($days_ago<30)
            $text='&lt;1м';
        elseif ($days_ago<60)
            $text='&lt;2м';
        elseif ($days_ago<90)
            $text='&lt;3м';
        elseif ($days_ago<180)
            $text='&lt;6м';
        elseif ($days_ago<365)
            $text='&lt;1р';
        elseif ($days_ago<365*2)
        {
            $text='&gt;1р';
            $class='text-danger font-weight-bold';
        }
        else
        {
            $text='&gt;2р';
            $class='text-danger font-weight-bold';
        }

        return sprintf('<span class="small %s">%s</span>', $class, $text);
    }
}
