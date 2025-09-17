<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table='articles';

    protected $fillable=[
        'title',
        'text',
    ];

    protected $casts=[
        'id'    => 'integer',
        'title' => 'string',
        'text'  => 'string',
    ];

    public function isVideo(): bool
    {
        return str_contains($this->text, 'youtube.com');
    }
}
