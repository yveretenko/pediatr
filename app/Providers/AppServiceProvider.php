<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Vaccine;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $services=[
            'Консультація педіатрична'                                                              => 700,
            'Консультація неврологічна'                                                             => 900,
            'Комбінована консультація (огляд педіатра та консультація з питань дитячої неврології)' => 1500,
            'Повторна консультація (в межах одного тижня, з приводу первинного звернення)'          => 500,
            'Первинний огляд новонародженого'                                                       => 1000,
            'Консультація з питань грудного вигодовування'                                          => 800,
            'Консультація з дитячого сну'                                                           => 1000,
            'Допологова консультація та підготовка до перших місяців життя дитини'                  => 1200,
            'Виклик лікаря додому (в межах міста)'                                                  => 2000,
        ];

        $vaccines=Vaccine::orderBy('name', 'asc')->get();

        View::share('services', $services);
        View::share('vaccines', $vaccines);
    }
}
