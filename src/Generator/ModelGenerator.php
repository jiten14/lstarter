<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModelGenerator
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function generate($name)
    {
        $name = ucfirst($name);
        $tableName = Str::plural(Str::snake($name));

        if (!Schema::hasTable($tableName)) {
            return [
                'status' => 'error',
                'message' => 'Migration table not found, create Migration before creating model.'
            ];
        }

        $columns = Schema::getColumnListing($tableName);
        $fillable = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $hasSoftDeletes = in_array('deleted_at', $columns);
        $modelTemplate = $this->getModelTemplate($name, $fillable, $hasSoftDeletes);

        $path = app_path("Models/{$name}.php");

        if (!$this->files->exists($path)) {
            $this->files->put($path, $modelTemplate);
            return [
                'status' => 'success',
                'message' => "Model {$name} created successfully."
            ];
        } else {
            return [
                'status' => 'warning',
                'message' => "Model {$name} already exists."
            ];
        }
    }

    protected function getModelTemplate($name, $fillable, $hasSoftDeletes)
    {
        $fillableFields = implode(",\n        ", array_map(function ($field) {
            return "'$field'";
        }, $fillable));

        $stub = $this->files->get(__DIR__ . '/../Templates/model.stub');

        $stub = str_replace('{{modelName}}', $name, $stub);
        $stub = str_replace('{{fillable}}', $fillableFields, $stub);

        if ($hasSoftDeletes) {
            $stub = str_replace('{{softDeletesImport}}', 'use Illuminate\Database\Eloquent\SoftDeletes;', $stub);
            $stub = str_replace('{{softDeletesTrait}}', ', SoftDeletes', $stub);
        } else {
            $stub = str_replace('{{softDeletesImport}}', '', $stub);
            $stub = str_replace('{{softDeletesTrait}}', '', $stub);
        }

        // Remove any remaining empty lines, but keep indentation
        $stub = preg_replace("/^\h*\v+/m", "", $stub);

        return $stub;
    }
}
