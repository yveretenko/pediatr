<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Vaccine;

class VaccineController extends Controller
{
    public function index()
    {
        return view('admin.vaccines.index');
    }

    public function filter(Request $request)
    {
        $columns=$request->input('columns', []);
        $order=$request->input('order.0', []);

        $column_data=$columns[$order['column']]['data'];
        $order_by = $column_data==='price' ? 'purchase_price' : $column_data;
        $order_dir = $order['dir'] ?? 'asc';

        $vaccines=Vaccine::orderBy($order_by, $order_dir)->get();

        $data=$vaccines->map(fn($vaccine) => [
            ...$vaccine->only([
                'id',
                'name',
                'type',
                'available',
                'comment',
                'country',
                'purchase_price',
                'price',
                'link'
            ]),
            'analogue_vaccine' => $vaccine->analogueVaccine?->name,
        ]);

        return response()->json([
            'data'            => $data,
            'recordsFiltered' => Vaccine::count(),
            'recordsTotal'    => Vaccine::count(),
        ]);
    }

    public function save(Request $request, Vaccine $vaccine)
    {
        $request->validate([
            'purchase_price' => 'required|integer|min:0',
            'available'      => 'boolean',
        ], [
            'purchase_price.*' => 'Некоректна закупочна ціна',
        ]);

        $vaccine
            ->fill([
                'purchase_price' => $request->input('purchase_price'),
                'available'      => $request->boolean('available'),
            ])
            ->save()
        ;

        return response()->json(['success' => true]);
    }
}
