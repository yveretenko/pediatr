<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DateComment extends Model
{
    use HasFactory;

    protected $table='date_comments';

    public $timestamps=false;

    protected $fillable=[
        'date',
        'comment',
    ];

    protected $casts=[
        'date' => 'date',
    ];
}
