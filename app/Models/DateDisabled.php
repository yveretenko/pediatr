<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateDisabled extends Model
{
    protected $table='dates_disabled';

    public $timestamps=false;

    protected $fillable=[
        'date',
    ];

    protected $casts=[
        'id'  =>'integer',
        'date'=>'date',
    ];
}
