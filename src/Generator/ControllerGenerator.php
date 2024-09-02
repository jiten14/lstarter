<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ControllerGenerator
{
    protected $files;
    protected $model;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function generate($name)
    {
        $modelName = Str::singular(Str::studly($name));
        $controllerName = "{$modelName}Controller";

        $modelClass = "App\\Models\\{$modelName}";
        if (!class_exists($modelClass)) {
            return [
                'status' => 'error',
                'message' => "Model {$modelName} not found. Please create the model first."
            ];
        }

        $this->model = new $modelClass();

        if (!$this->model instanceof Model) {
            return [
                'status' => 'error',
                'message' => "{$modelName} is not a valid Eloquent model."
            ];
        }

        $tableName = $this->model->getTable();

        $columns = Schema::getColumnListing($tableName);
        $fillable = $this->model->getFillable();

        $rules = $this->generateValidationRules($columns);

        $controllerTemplate = $this->getControllerTemplate($controllerName, $modelName, $fillable, $rules);

        $path = app_path("Http/Controllers/{$controllerName}.php");

        if (!$this->files->exists($path)) {
            $this->files->put($path, $controllerTemplate);
            return [
                'status' => 'success',
                'message' => "Controller {$controllerName} created successfully."
            ];
        } else {
            return [
                'status' => 'warning',
                'message' => "Controller {$controllerName} already exists."
            ];
        }
    }

    protected function generateValidationRules($columns)
    {
        $rules = [];

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // Only adding "required" rule to each field
            $rules[$column] = ['required'];
        }

        return $rules;
    }

    protected function getControllerTemplate($controllerName, $modelName, $fillable, $rules)
    {
        $stub = $this->files->get(__DIR__ . '/../Templates/controller.stub');

        $stub = str_replace('{{controllerName}}', $controllerName, $stub);
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{fillable}}', implode(", ", $fillable), $stub);

        // Convert model name to lowercase for the view path
        $lowercaseModelName = Str::lower(Str::plural($modelName));
        $stub = str_replace('{{viewPath}}', $lowercaseModelName, $stub);

        $validationRules = $this->formatValidationRules($rules);
        $stub = str_replace('{{validationRules}}', $validationRules, $stub);

        return $stub;
    }

    protected function formatValidationRules($rules)
    {
        $formattedRules = "[\n";
        foreach ($rules as $field => $fieldRules) {
            $formattedRules .= "            '{$field}' => ['required'],\n";
        }
        $formattedRules .= "        ]";
        return $formattedRules;
    }
}
