<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;

class ViewIndexGenerator
{
    protected $files;
    protected $excludedColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

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
        $viewFilePath = resource_path("views/{$viewFolderName}/index.blade.php");

        if (!$this->files->exists($viewFilePath)) {
            $this->createViewFolder($viewFolderName);
            $this->createViewFile($viewFilePath, $modelName, $modelNameLower, $modelNames, $viewFolderName);
            return [
                'status' => 'success',
                'message' => "View folder and index.blade.php file created successfully for {$modelName}."
            ];
        } else {
            return [
                'status' => 'warning',
                'message' => "View folder and index.blade.php file already exists for {$modelName}."
            ];
        }
    }

    protected function createViewFolder($viewFolderName)
    {
        $this->files->makeDirectory(resource_path("views/{$viewFolderName}"), 0755, true);
    }

    protected function createViewFile($viewFilePath, $modelName, $modelNameLower, $modelNames, $viewFolderName)
    {
        $stub = $this->files->get(__DIR__ . '/../Templates/view-index.stub');
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{modelNameLower}}', $modelNameLower, $stub);
        $stub = str_replace('{{modelNames}}', $modelNames, $stub);
        $stub = str_replace('{{viewFolderName}}', $viewFolderName, $stub);
        
        $columns = $this->getTableColumns($modelName);
        $stub = str_replace('{{tableHeaders}}', $this->generateTableHeaders($columns), $stub);
        $stub = str_replace('{{tableRows}}', $this->generateTableRows($columns, $modelNameLower), $stub);
        
        $this->files->put($viewFilePath, $stub);
    }

    protected function getTableColumns($modelName)
    {
        $model = "App\\Models\\{$modelName}";
        $allColumns = Schema::getColumnListing((new $model)->getTable());
        return array_diff($allColumns, $this->excludedColumns);
    }

    protected function generateTableHeaders($columns)
    {
        $headers = '';
        foreach ($columns as $column) {
            $headers .= "                <th>" . Str::title(str_replace('_', ' ', $column)) . "</th>\n";
        }
        return $headers;
    }

    protected function generateTableRows($columns, $modelNameLower)
    {
        $rows = '';
        foreach ($columns as $column) {
            $rows .= "                    <td>{{ \${$modelNameLower}->{$column} }}</td>\n";
        }
        return $rows;
    }
}