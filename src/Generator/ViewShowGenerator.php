<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

class ViewShowGenerator
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
        $viewFolderName = Str::plural($modelNameLower);
        $viewFilePath = resource_path("views/{$viewFolderName}/show.blade.php");

        if (!$this->files->exists($viewFilePath)) {
            $this->createViewFile($viewFilePath, $modelName, $modelNameLower, $viewFolderName);
            return [
                'status' => 'success',
                'message' => "show.blade.php file created successfully for {$modelName}."
            ];
        } else {
            return [
                'status' => 'warning',
                'message' => "show.blade.php file already exists for {$modelName}."
            ];
        }
    }

    protected function createViewFile($viewFilePath, $modelName, $modelNameLower, $viewFolderName)
    {
        $stub = $this->files->get(__DIR__ . '/../Templates/view-show.stub');
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{modelNameLower}}', $modelNameLower, $stub);
        $stub = str_replace('{{viewFolderName}}', $viewFolderName, $stub);
        
        $columns = $this->getTableColumns($modelName);
        $stub = str_replace('{{modelFields}}', $this->generateModelFields($columns, $modelNameLower), $stub);
        
        $this->files->put($viewFilePath, $stub);
    }

    protected function getTableColumns($modelName)
    {
        $model = "App\\Models\\{$modelName}";
        return Schema::getColumnListing((new $model)->getTable());
    }

    protected function generateModelFields($columns, $modelNameLower)
    {
        $fields = '';
        foreach ($columns as $column) {
            $fieldName = Str::title(str_replace('_', ' ', $column));
            $fields .= "    <div class=\"mb-3\">\n";
            $fields .= "        <strong>{$fieldName}:</strong> {{ \${$modelNameLower}->{$column} }}\n";
            $fields .= "    </div>\n";
        }
        return $fields;
    }
}