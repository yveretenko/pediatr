<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    protected $table='appointments';

    protected $dateFormat='U';

    public static function boot()
    {
        parent::boot();

        static::creating(function($model){
            $model->created_at=time();
            $model->updated_at=null;
        });

        static::updating(function($model){
            $model->updated_at=time();
        });
    }

    protected $fillable=[
        'date',
        'name',
        'tel',
        'comment',
        'file',
        'sms_notified',
        'neurology',
        'earlier',
        'online',
        'call_back',
    ];

    protected $casts=[
        'date'         => 'integer',
        'sms_notified' => 'integer',
        'neurology'    => 'boolean',
        'earlier'      => 'boolean',
        'online'       => 'boolean',
        'call_back'    => 'boolean',
        'created_at'   => 'integer',
        'updated_at'   => 'integer',
    ];

    public function vaccines(): BelongsToMany
    {
        return $this->belongsToMany(Vaccine::class, 'appointment_vaccines', 'appointment_id', 'vaccine_id')->orderBy('name');
    }

    public function getCommentFormattedAttribute(): string
    {
        return implode("<br>", array_map(function($line){
            if (str_starts_with($line, '!'))
                return '<span class="text-danger font-weight-bold">'.substr($line, 1).'</span>';

            return $line;
        }, explode("\n", $this->comment)));
    }

    public function isToday(): bool
    {
        return $this->date>=strtotime('today') && $this->date<strtotime('tomorrow');
    }

    public function isTomorrow(): bool
    {
        return $this->date>=strtotime('tomorrow') && $this->date<strtotime('tomorrow +1 day');
    }

    public static function thirdVisitsYesterday(): Collection
    {
        return self
            ::select('tel')
            ->whereNotNull('tel')
            ->where('online', false)
            ->groupBy('tel')
            ->havingRaw('COUNT(*) = 3')
            ->havingRaw('MAX(date) BETWEEN ? AND ?', [strtotime('yesterday'), strtotime('today')])
            ->get()
        ;
    }

    public function visits()
    {
        return $this->hasMany(Appointment::class, 'tel', 'tel');
    }
}
