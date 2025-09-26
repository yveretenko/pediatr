<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatesDisabledService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatesDisabledController extends Controller
{
    public function __construct(protected DatesDisabledService $service) {}

    public function index()
    {
        $close_dates=$this->service->getAllDates();

        return view('admin.dates_disabled.index', compact('close_dates'));
    }

    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'dates'   => 'required|array',
            'dates.*' => 'required|string',
        ]);

        $success=$this->service->saveDates($request->input('dates'));

        return response()->json(['success' => $success]);
    }
}
