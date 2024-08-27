<?php

namespace Jiten14\Lstarter;

use Illuminate\Support\ServiceProvider;
use Jiten14\Lstarter\Console\GenerateLayout;

class LstarterServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the command
        $this->commands([
            GenerateLayout::class,
        ]);
    }

    public function boot()
    {
        // Publish the layouts if needed
        $this->publishes([
            __DIR__ . '/Layouts/layout.blade.php' => resource_path('views/layouts/layout.blade.php'),
        ], 'layouts');
    }
}
