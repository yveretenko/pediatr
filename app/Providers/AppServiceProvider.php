<?php

namespace App\Providers;

use App\Services\VaccineService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(VaccineService $vaccineService): void
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

        $vaccines=$vaccineService->allOrderedByName();

        View::share('services', $services);
        View::share('vaccines', $vaccines);

        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient($config['authorization_token']);
            $adapter = new DropboxAdapter($client);
            $filesystem = new Filesystem($adapter);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
