<?php

namespace Jiten14\Lstarter;

use Illuminate\Support\ServiceProvider;
use Jiten14\Lstarter\Console\GeneratePackage;
use Jiten14\Lstarter\Console\GenerateMigration;
use Jiten14\Lstarter\Console\GenerateModel;
use Jiten14\Lstarter\Console\GenerateRelation;
use Jiten14\Lstarter\Console\GenerateFactory;
use Jiten14\Lstarter\Console\GenerateController;
use Jiten14\Lstarter\Console\GenerateLayout;

class LstarterServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register the command
        $this->commands([
            GeneratePackage::class,
            GenerateMigration::class,
            GenerateModel::class,
            GenerateRelation::class,
            GenerateFactory::class,
            GenerateController::class,
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
