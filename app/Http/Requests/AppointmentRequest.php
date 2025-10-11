<?php

namespace App\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Helpers\StringHelper;
use App\Models\Appointment;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'date' => ['required', function($attribute, $value, $fail){
                try
                {
                    Carbon::parse($value);
                }
                catch (Exception)
                {
                    $fail('Невірна дата');
                }
            }],
            'time' => ['required', function($attribute, $value, $fail){
                [$hour, $minute]=explode(':', $value);

                if ($hour<8 || ($hour>21 || ($hour==21 && $minute>0)))
                    $fail('Введіть час між 8:00 та 21:00');
            }],
            'tel' => ['nullable', function($attribute, $value, $fail){
                $tel=StringHelper::normalizeTelephone($value);

                if ($value && strlen($tel)!==10)
                    $fail('Невірний номер телефону');
            }],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function($validator){
            $date=$this->date;
            $time=$this->time;
            $id=$this->id;

            if ($date && $time)
            {
                $timestamp=strtotime("$date $time");

                $exists=Appointment::
                    where('date', $timestamp)
                    ->when($id, fn($q) => $q->where('id', '!=', $id))
                    ->exists()
                ;

                if ($exists)
                    $validator->errors()->add('date', 'На цей час вже є запис');
            }
        });
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Введіть дату',
            'time.required' => 'Введіть час',
        ];
    }
}
