<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table='blacklist';

    protected $primaryKey='tel';

    public $incrementing=false;

    protected $keyType='string';

    public $timestamps=false;

    protected $fillable=[
        'tel',
        'reason',
        'name',
    ];

    public function getReasonAndNameAttribute(): ?string
    {
        if ($this->reason && $this->name)
        {
            return "{$this->reason} ({$this->name})";
        }

        return $this->reason ?? $this->name;
    }
}
