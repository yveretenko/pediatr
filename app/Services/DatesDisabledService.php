<?php

namespace App\Services;

use App\Models\DateDisabled;
use DateTime;
use Exception;

class DatesDisabledService
{
    public function getAllDates(): array
    {
        return DateDisabled::all()->map(fn($d) => $d->date->format('d.m.Y'))->toArray();
    }

    public function saveDates(array $dates): bool
    {
        try
        {
            DateDisabled::truncate();

            foreach ($dates as $date_str)
            {
                $date_parts=array_slice(explode(' ', $date_str), 1, 3);
                $date=new DateTime(implode(' ', $date_parts));
                DateDisabled::create(['date' => $date]);
            }

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }
}
