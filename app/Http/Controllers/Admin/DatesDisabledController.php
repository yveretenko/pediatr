<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DateDisabled;
use DateTime;
use Exception;
use Illuminate\Http\Request;

class DatesDisabledController extends Controller
{
    public function index()
    {
        $dates_disabled=DateDisabled::all();

        $close_dates=$dates_disabled->map(fn($d) => $d->date->format('d.m.Y'));

        return view('admin.dates_disabled.index', compact('close_dates'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'dates'   => 'required|array',
            'dates.*' => 'required|string',
        ]);

        $success=true;

        try
        {
            DateDisabled::truncate();

            foreach ($request->input('dates') as $date_str)
            {
                $date_parts=array_slice(explode(' ', $date_str), 1, 3);
                $date = new DateTime(implode(' ', $date_parts));

                DateDisabled::create(['date' => $date]);
            }
        }
        catch (Exception)
        {
            $success=false;
        }

        return response()->json([
            'success' => $success,
        ]);
    }
}
