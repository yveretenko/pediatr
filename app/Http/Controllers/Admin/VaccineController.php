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
        $columns=$request->input('columns', []);
        $order=$request->input('order.0', []);

        $column_data=$columns[$order['column']]['data'] ?? 'id';

        $order_params=[
            'column' => $column_data,
            'dir'    => $order['dir'] ?? 'asc',
        ];

        return response()->json($this->vaccineService->getFiltered($order_params));
    }

    public function save(Request $request, Vaccine $vaccine): JsonResponse
    {
        $request->validate([
            'purchase_price' => 'required|integer|min:0',
            'available'      => 'boolean',
        ], [
            'purchase_price.*' => 'Некоректна закупочна ціна',
        ]);

        $this->vaccineService->update($vaccine, $request->only(['purchase_price', 'available']));

        return response()->json(['success' => true]);
    }
}
