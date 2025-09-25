<?php

namespace App\Services;

use App\Models\Vaccine;
use Illuminate\Database\Eloquent\Collection;

class VaccineService
{
    public function getFiltered(array $order=[]): array
    {
        $order_by=$order['column'] ?? 'id';
        $order_dir=$order['dir'] ?? 'asc';

        if ($order_by==='price')
            $order_by='purchase_price';

        $vaccines=Vaccine::orderBy($order_by, $order_dir)->get();

        $data=$vaccines->map(fn(Vaccine $vaccine) => [
            ...$vaccine->only([
                'id',
                'name',
                'type',
                'available',
                'comment',
                'country',
                'purchase_price',
                'price',
                'link',
            ]),
            'analogue_vaccine' => $vaccine->analogueVaccine?->name,
        ])->toArray();

        return [
            'data'            => $data,
            'recordsFiltered' => $vaccines->count(),
            'recordsTotal'    => Vaccine::count(),
        ];
    }

    public function formatForApi(Collection $vaccines): array
    {
        return $vaccines->map(fn($vaccine) => $vaccine->only([
            'id',
            'name',
            'short_name',
            'available',
            'type',
            'comment',
            'country',
            'purchase_price',
            'price',
            'link',
        ]))->toArray();
    }

    public function syncAppointmentVaccines($appointment, array $vaccineIds): void
    {
        $appointment->vaccines()->sync($vaccineIds);
    }

    public function allOrderedByName(): Collection
    {
        return Vaccine::orderBy('name', 'asc')->get();
    }

    public function update(Vaccine $vaccine, array $data): Vaccine
    {
        $vaccine->fill([
            'purchase_price' => $data['purchase_price'] ?? $vaccine->purchase_price,
            'available'      => $data['available'] ?? $vaccine->available,
        ])->save();

        return $vaccine;
    }
}
