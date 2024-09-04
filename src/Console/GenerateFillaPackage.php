<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateFillaPackage extends Command
{
    protected $signature = 'generate:filla-package {tablename}';

    protected $description = 'Generate migration, run migration, and generate model';

    public function handle()
    {
        // Check if the Filament package is installed
        if (!class_exists(\Filament\FilamentServiceProvider::class)) {
            $this->error('Install the required package first: Filament 3.2');
            return Command::FAILURE;
        }
        
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

        // Step 3: Generate model
        $modelName = Str::studly($tableName);
        $this->info("Generating model for {$modelName}...");
        $exitCode = $this->call("generate:model", ['name' => $modelName]);
        
        if ($exitCode !== 0) {
            $this->error('Model generation failed.');
            return;
        }

        // Step 4: Generate relation
        $this->call('generate:relation', [
            'model' => $modelName,
        ]);

        // Step 5: Generate factory & seeder
        $this->call('generate:factory', [
            'model' => $modelName,
        ]);

        // Step 6: Seeding to database
        $seederClass = "{$modelName}Seeder";
        $this->info("Seeding database using {$seederClass}...");
        Artisan::call('db:seed', ['--class' => $seederClass]);

        // Step 7: Generate Fillament Resource
        $dbtable = Str::plural(Str::snake($modelName));
        if (!Schema::hasTable($dbtable)) {
            $this->error('Migration table not found.');
            return Command::FAILURE;
        }
        $columns = Schema::getColumnListing($dbtable);
        $hasSoftDeletes = in_array('deleted_at', $columns);
        if ($hasSoftDeletes) {
            $this->call('make:filament-resource', [
                'name'=> $modelName,
                '--soft-deletes'=> true,
                '--generate'=> true,
            ]);
        } else {
            $this->call('make:filament-resource', [
                'name' => $modelName,
                '--generate' => true,
            ]);
        }
        
        // Step 8: Upadate resource
        $this->call('advance:update-resource', [
            'model' => $modelName,
        ]);

        $this->info('Fillament Resource Package generation completed successfully!');

    }
}