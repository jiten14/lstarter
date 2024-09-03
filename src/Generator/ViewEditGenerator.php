<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;

class ViewEditGenerator
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function generate($name)
    {
        $modelName = Str::studly($name);
        $modelNameLower = Str::lower($modelName);
        $modelNames = Str::plural($modelNameLower);
        $viewFolderName = $modelNames;
        $viewFilePath = resource_path("views/{$viewFolderName}/edit.blade.php");

        if (!$this->files->exists($viewFilePath)) {
            $this->createViewFile($viewFilePath, $modelName, $modelNameLower, $modelNames, $viewFolderName);
            return [
                'status' => 'success',
                'message' => "edit.blade.php file created successfully for {$modelName}."
            ];
        } else {
            return [
                'status' => 'warning',
                'message' => "edit.blade.php file already exists for {$modelName}."
            ];
        }
    }

    protected function createViewFile($viewFilePath, $modelName, $modelNameLower, $modelNames, $viewFolderName)
    {
        $model = $this->getModel($modelName);
        $fillableFields = $this->getFillableFields($model);

        $stub = $this->files->get(__DIR__ . '/../Templates/view-edit.stub');
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{modelNameLower}}', $modelNameLower, $stub);
        $stub = str_replace('{{modelNames}}', $modelNames, $stub);
        $stub = str_replace('{{viewFolderName}}', $viewFolderName, $stub);
        $stub = str_replace('{{fillableFields}}', $this->renderFillableFields($fillableFields, $modelNameLower), $stub);
        $this->files->put($viewFilePath, $stub);
    }

    protected function getModel($modelName)
    {
        $modelClass = "App\\Models\\{$modelName}";
        if (!class_exists($modelClass)) {
            throw new \Exception("Model {$modelClass} not found.");
        }
        return new $modelClass;
    }

    protected function getFillableFields($model)
    {
        if (method_exists($model, 'getFillable')) {
            return $model->getFillable();
        } else {
            return $this->getTableColumns(get_class($model));
        }
    }

    protected function getTableColumns($modelClass)
    {
        $table = (new $modelClass)->getTable();
        return \DB::getSchemaBuilder()->getColumnListing($table);
    }

    protected function renderFillableFields($fields, $modelNameLower)
    {
        $html = '';
        foreach ($fields as $field) {
            $html .= $this->renderFormField($field, $modelNameLower);
        }
        return $html;
    }

    protected function renderFormField($field, $modelNameLower)
    {
        return <<<HTML
        <div class="form-group">
            <label for="{$field}">{$this->formatLabel($field)}</label>
            <input type="text" class="form-control" id="{$field}" name="{$field}" value="{{ old('{$field}', \${$modelNameLower}->{$field}) }}" required>
        </div>
        HTML;
    }

    protected function formatLabel($field)
    {
        return ucwords(str_replace('_', ' ', $field));
    }
}