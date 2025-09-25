<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class CalendarService
{
    public function getWeeks(array $working_days, int $days_range): array
    {
        $dates=$this->getDatesRange($days_range, max($working_days));

        $weeks=[];
        foreach ($dates as $date)
        {
            if (!in_array($date->dayOfWeekIso, $working_days))
                continue;

            $weeks[$date->weekOfYear][]=$date;
        }

        return $weeks;
    }

    public function getDatesRange(int $days_range, int $weekEndDay): CarbonPeriod
    {
        $today=Carbon::today();

        $start_date = $today->dayOfWeekIso<=$weekEndDay ? $today->startOfWeek(CarbonInterface::MONDAY) : $today->next(CarbonInterface::MONDAY);

        $end_date=$start_date->copy()->addDays($days_range-1);

        return CarbonPeriod::create($start_date, '1 day', $end_date);
    }
}
