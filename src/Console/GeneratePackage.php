<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GeneratePackage extends Command
{
    protected $signature = 'generate:package';

    protected $description = 'Generate migration, run migration, and generate model';

    public function handle()
    {
        // Step 1: Generate migration
        $this->info('Generating migration...');
        $exitCode = $this->call('generate:migration', ['--mo' => true]);
        
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

        // Step 3: Get the latest migration file
        $migrationPath = database_path('migrations');
        $files = scandir($migrationPath, SCANDIR_SORT_DESCENDING);
        $latestMigration = $files[0];

        // Extract table name from migration file
        preg_match('/create_(\w+)_table/', $latestMigration, $matches);
        $tableName = $matches[1] ?? null;

        if (!$tableName) {
            $this->error('Could not determine table name from migration file.');
            return;
        }

        // Step 4: Generate model
        $modelName = Str::studly(Str::singular($tableName));
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

        // Execute the generate:factory command using the modelName
        $this->call('generate:controller', [
            'model' => $modelName,
        ]);

        $this->info('Package generation completed successfully!');
    }
}