<?php

namespace App\Http\Controllers\Admin;

use App\DTO\OrderDTO;
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
        $order_params=$request->input('order.0', []);
        $columns=$request->input('columns', []);

        $column_data = $columns[$order_params['column']]['data'] ?? 'id';
        $dir = $order_params['dir'] ?? 'asc';

        $orderDTO = new OrderDTO(column: $column_data, dir: $dir);

        return response()->json($this->vaccineService->getFiltered($orderDTO));
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
