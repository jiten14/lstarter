<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

class FactoryGenerator
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function generate($model)
    {
        $modelClass = "App\\Models\\{$model}";
        $tableName = Str::snake(Str::plural($model));
        $factoryPath = database_path("factories/{$model}Factory.php");
        $seederPath = database_path("seeders/{$model}Seeder.php");

        if (!$this->files->exists(app_path("Models/{$model}.php"))) {
            return [
                'status' => 'error',
                'message' => "Model {$model} not found."
            ];
        }

        if (!$this->files->exists(database_path("migrations/"))) {
            return [
                'status' => 'error',
                'message' => "Migration for {$model} not found."
            ];
        }

        $fields = $this->getFieldsFromMigration($tableName);

        $this->buildFactoryClass($modelClass, $fields, $factoryPath);

        $this->createModelSeeder($modelClass, $model, $seederPath);

        return [
            'status' => 'success',
            'message' => "Factory created and {$model}Seeder class created."
        ];
    }

    protected function getFieldsFromMigration($tableName)
    {
        $columns = Schema::getColumnListing($tableName);
        $fields = [];

        foreach ($columns as $column) {
            // Skip specific columns
            if (in_array($column, ['id', 'deleted_at', 'updated_at', 'created_at'])) {
                continue; // Skip these columns
            }
    
            $factoryFieldType = $this->getFactoryFieldType($column);
            if ($factoryFieldType !== null) {
                $fields[$column] = $factoryFieldType;
            }
        }

        return $fields;
    }

    protected function getFactoryFieldType($column)
    {
        if (Str::contains($column, 'email')) {
            return "fake()->unique()->safeEmail()";
        }

        if (Str::contains($column, 'password')) {
            return "bcrypt(fake()->password())";
        }

        if (Str::contains($column, '_name') || Str::contains($column, 'name')) {
            if (Str::contains($column, 'company_name')) {
                return "fake()->company()";
            }
            return "fake()->name()";
        }

        if (Str::contains($column, ['address', '_address'])) {
            return "fake()->address()";
        }

        if (Str::contains($column, 'phone')) {
            return "fake()->phoneNumber()";
        }

        if (Str::contains($column, ['date', '_date'])) {
            return "fake()->dateTime()";
        }

        if (Str::contains($column, ['text', 'description'])) {
            return "fake()->paragraph()";
        }

        if (Str::contains($column, ['price', 'amount'])) {
            return "fake()->randomFloat(2, 10, 1000)";
        }

        if (Str::contains($column, ['created_at', 'updated_at','deleted_at'])) {
            return "fake()->dateTime()";
        }

        if (Str::contains($column, 'image')) {
            return "'https://placehold.co/600x400'";
        }

        if (Str::endsWith($column, '_id')) {
            return "rand(1, 10)";
        }

        if (Str::contains($column, ['count', '_count'])) {
            return "rand(1, 100)";
        }

        if (Str::contains($column, ['status', '_status'])) {
            return "rand(0, 1)";
        }

        if (Str::contains($column, ['is_'])) {
            return "rand(0, 1)";
        }

        return "fake()->word()";
    }

    protected function buildFactoryClass($modelClass, $fields, $factoryPath)
    {
        $factoryStub = $this->files->get(__DIR__.'/../Templates/factory.stub');

        $factoryFields = implode(",\n            ", array_map(
            fn($value, $key) => "'$key' => $value",
            $fields,
            array_keys($fields)
        ));

        $factoryClassName = class_basename($modelClass).'Factory';

        $factoryContent = str_replace(
            ['{{ modelNamespace }}', '{{ factoryClassName }}', '{{ modelClass }}', '{{ factoryFields }}'],
            [$modelClass, $factoryClassName, $modelClass, $factoryFields],
            $factoryStub
        );

        $this->files->put($factoryPath, $factoryContent);
    }

    protected function createModelSeeder($modelClass, $model, $seederPath)
    {
        $seederStub = $this->files->get(__DIR__.'/../Templates/seeder.stub');

        $modelName = class_basename($modelClass);
        $factoryCall = "{$modelName}::factory(10)->create();";
        $modelImport = "use {$modelClass};";

        $seederContent = str_replace(
            ['{{ modelNamespace }}', '{{ modelClass }}', '{{ factoryCall }}'],
            [$modelClass, $modelName, $factoryCall],
            $seederStub
        );

        if (!$this->files->exists($seederPath)) {
            $this->files->put($seederPath, $seederContent);
        } else {
            $seederContent = $this->files->get($seederPath);
            
            // Ensure model import in the header with correct formatting
            if (!str_contains($seederContent, $modelImport)) {
                // Find the last use statement and insert the new model import after it
                $pattern = '/(use Illuminate\\\\Database\\\\Seeder;)(?!.*use)/';
                $replacement = "$1\n$modelImport";
                $seederContent = preg_replace($pattern, $replacement, $seederContent);
            }

            // Add factory call within the run method
            if (!str_contains($seederContent, $factoryCall)) {
                $pattern = '/(public function run\(\): void\s*\{)([^\}]*)(\})/s';
                $replacement = "$1\n$factoryCall$2$3";
                $seederContent = preg_replace($pattern, $replacement, $seederContent);
            }

            // Ensure correct formatting
            $seederContent = preg_replace('/\n{3,}/', "\n\n", $seederContent); // Remove extra blank lines
            $seederContent = preg_replace('/(use Illuminate\\\\Database\\\\Seeder;)\n+/', "$1\n", $seederContent); // Remove blank line after Seeder import
            $seederContent = preg_replace('/(use App\\\\Models\\\\[^;]+;\n)+/', "$0", $seederContent); // Do not add blank line after last model import
            $seederContent = preg_replace('/^(use [^;]+;\n)+class/m', "$1\nclass", $seederContent); // Add blank line before class declaration

            $this->files->put($seederPath, $seederContent);
        }
    }
}
