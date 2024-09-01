<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\MigrationGenerator;

class GenerateMigration extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'generate:migration {--mo : Add modifiers to fields}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a custom migration file with advanced options and optional relations';

    /**
     * The MigrationGenerator instance.
     *
     * @var \Jiten14\Lstarter\Generator\MigrationGenerator
     */
    protected $migrationGenerator;

    /**
     * Create a new command instance.
     *
     * @param \Jiten14\Lstarter\Generator\MigrationGenerator $migrationGenerator
     */
    public function __construct(MigrationGenerator $migrationGenerator)
    {
        parent::__construct();
        $this->migrationGenerator = $migrationGenerator;
    }

    public function handle()
    {
        // Ask for table name
        $tableName = $this->ask('Enter table name (in all lowercase & singular):');

        // Ask for fields
        $fields = $this->ask('Enter fields with type (comma separated, e.g., name:string, email:string):');

        // Ask for modifiers if --mo flag is present
        $modifiers = '';
        if ($this->option('mo')) {
            $modifiers = $this->ask('Enter field modifiers (comma separated, e.g., seo_title:nullable, view_count:default(0)):') ?? '';
        }

        // Ask for relations (optional)
        $relations = $this->ask('Enter relations (optional, comma separated, e.g., category:categories,user:users):') ?? '';
        
        // Ask for Soft Delete (optional)
        $softDeletes = $this->confirm('Do you want to add soft deletes to this migration?', false);

        // Generate the migration
        $this->migrationGenerator->generate($tableName, $fields, $modifiers, $relations, $softDeletes);

        $this->info('Migration file generated successfully!');
    }
}