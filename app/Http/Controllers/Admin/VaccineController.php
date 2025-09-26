<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\VaccineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Vaccine;

class VaccineController extends Controller
{
    public function __construct(protected VaccineService $vaccineService) {}

    public function index()
    {
        return view('admin.vaccines.index');
    }

    public function filter(Request $request): JsonResponse
    {
        $orderParams=$request->input('order.0', []);
        $columns=$request->input('columns', []);
        $columnData = $columns[$orderParams['column']]['data'] ?? 'id';

        return response()->json($this->vaccineService->getFiltered(['column' => $columnData, 'dir' => $orderParams['dir'] ?? 'asc',]));
    }

    public function save(Request $request, Vaccine $vaccine): JsonResponse
    {
        $validated=$request->validate([
            'purchase_price' => 'required|integer|min:0',
        ], [
            'purchase_price.*' => 'Некоректна закупочна ціна',
        ]);

        $validated['available']=$request->boolean('available');

        $this->vaccineService->update($vaccine, $validated);

        return response()->json(['success' => true]);
    }
}
