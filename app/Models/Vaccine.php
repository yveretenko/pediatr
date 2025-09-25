<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccine extends Model
{
    protected $table='vaccines';

    public $timestamps=false;

    protected $fillable=[
        'name',
        'short_name',
        'type',
        'country',
        'available',
        'purchase_price',
        'link',
        'analogue_vaccine_id',
        'comment',
    ];

    protected $casts=[
        'available'      => 'boolean',
        'purchase_price' => 'integer',
    ];

    public function analogueVaccine(): BelongsTo
    {
        return $this->belongsTo(Vaccine::class, 'analogue_vaccine_id');
    }

    public function getPriceAttribute(): ?int
    {
        if ($this->purchase_price)
        {
            if ($this->purchase_price>500)
                $price=ceil((($this->purchase_price+700)*1.06)/100)*100;
            else
                $price=ceil((($this->purchase_price+450)*1.06)/50)*50;
        }

        return isset($price) ? (int)$price : null;
    }
}
