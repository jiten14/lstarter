<?php

namespace Jiten14\Lstarter;

use Illuminate\Support\ServiceProvider;
use Jiten14\Lstarter\Console\GeneratePackage;
use Jiten14\Lstarter\Console\GenerateMigration;
use Jiten14\Lstarter\Console\GenerateModel;
use Jiten14\Lstarter\Console\GenerateRelation;
use Jiten14\Lstarter\Console\GenerateFactory;
use Jiten14\Lstarter\Console\GenerateController;
use Jiten14\Lstarter\Console\GenerateRoutes;
use Jiten14\Lstarter\Console\GenerateLayout;
use Jiten14\Lstarter\Console\GenerateIndexView;
use Jiten14\Lstarter\Console\GenerateCreateView;
use Jiten14\Lstarter\Console\GenerateEditView;
use Jiten14\Lstarter\Console\GenerateShowView;
//Fillament Commands
use Jiten14\Lstarter\Console\FillamentSetup;
use Jiten14\Lstarter\Console\AdvanceUsers;
use Jiten14\Lstarter\Console\AdvanceUsersSeed;
use Jiten14\Lstarter\Console\AdvanceAuth;
use Jiten14\Lstarter\Console\AdvanceRole;
use Jiten14\Lstarter\Console\AdvancePermission;
use Jiten14\Lstarter\Console\AdvancePolicy;
use Jiten14\Lstarter\Console\AdvanceFillament;
use Jiten14\Lstarter\Console\GenerateFillaPackage;
use Jiten14\Lstarter\Console\AdvanceUpdateResource;


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
            GenerateRoutes::class,
            GenerateLayout::class,
            GenerateIndexView::class,
            GenerateCreateView::class,
            GenerateEditView::class,
            GenerateShowView::class,
            FillamentSetup::class,//Fillament Commands
            AdvanceUsers::class,
            AdvanceUsersSeed::class,
            AdvanceAuth::class,
            AdvanceRole::class,
            AdvancePermission::class,
            AdvancePolicy::class,
            AdvanceFillament::class,
            GenerateFillaPackage::class,
            AdvanceUpdateResource::class,
        ]);

        // Load the helpers file
        $this->loadHelpers();

    }

    protected function loadHelpers()
    {
        $helpersFile = __DIR__ . '/helpers.php';

        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }

    public function boot()
    {
        // Publish the layouts if needed
        $this->publishes([
            __DIR__ . '/Layouts/layout.blade.php' => resource_path('views/layouts/layout.blade.php'),
        ], 'layouts');
    }
}
