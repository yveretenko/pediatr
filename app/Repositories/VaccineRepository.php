<?php

namespace App\Repositories;

use App\Models\Vaccine;

class VaccineRepository
{
    public function getFiltered(string $order_by, string $order_dir='asc')
    {
        return Vaccine::orderBy($order_by, $order_dir)->get();
    }
}
