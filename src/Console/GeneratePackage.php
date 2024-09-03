<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GeneratePackage extends Command
{
    protected $signature = 'generate:package {tablename}';

    protected $description = 'Generate migration, run migration, and generate model';

    public function handle()
    {
        $tableName = $this->argument('tablename');

        // Step 1: Generate migration
        $this->info('Generating migration...');
        $exitCode = $this->call('generate:migration', ['tablename' => $tableName, '--mo' => true]);
        
        if ($exitCode !== 0) {
            $this->error('Migration generation failed. Aborting package generation.');
            return;
        }

        // Step 2: Run migration
        $this->info('Running migration...');
        $exitCode = $this->call('migrate');
        
        if ($exitCode !== 0) {
            $this->error('Migration failed. Aborting package generation.');
            return;
        }

        // Step 4: Generate model
        $modelName = Str::studly($tableName);
        $this->info("Generating model for {$modelName}...");
        $exitCode = $this->call("generate:model", ['name' => $modelName]);
        
        if ($exitCode !== 0) {
            $this->error('Model generation failed.');
            return;
        }

        // Execute the generate:relation command using the modelName
        $this->call('generate:relation', [
            'model' => $modelName,
        ]);

        // Execute the generate:factory command using the modelName
        $this->call('generate:factory', [
            'model' => $modelName,
        ]);

        // Step 5: Seed the database with the specific model seeder
        $seederClass = "{$modelName}Seeder";
        $this->info("Seeding database using {$seederClass}...");
        Artisan::call('db:seed', ['--class' => $seederClass]);

        // Execute the generate:controller command using the modelName
        $this->call('generate:controller', [
            'model' => $modelName,
        ]);

        // Execute the generate:routes command using the modelName
        $this->call('generate:routes', [
            'model' => $modelName,
        ]);

        // Execute the generate:layout command to copy layout.blade.php
        $this->call('generate:layout');

        // Execute the generate:view-index command using the modelName
        $this->call('generate:view-index', [
            'model' => $modelName,
        ]);

        // Execute the generate:view-create command using the modelName
        $this->call('generate:view-create', [
            'model' => $modelName,
        ]);

        // Execute the generate:view-edit command using the modelName
        $this->call('generate:view-edit', [
            'model' => $modelName,
        ]);

        // Execute the generate:view-show command using the modelName
        $this->call('generate:view-show', [
            'model' => $modelName,
        ]);

        $this->info('Package generation completed successfully!');
    }
}