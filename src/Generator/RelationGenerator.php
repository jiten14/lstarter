<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Filesystem\Filesystem;

class RelationGenerator
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function generate($model, $relationshipType = null, $relatedModel = null)
    {
        $modelPath = app_path("Models/{$model}.php");

        if (!$this->files->exists($modelPath)) {
            return [
                'status' => 'error',
                'message' => "Model {$model} not found."
            ];
        }

        // If either relationship type or related model is provided, proceed to add the relationship
        if ($relationshipType && $relatedModel) {
            $relatedModel = Str::studly(Str::singular($relatedModel));
            $relatedModelClass = "App\\Models\\{$relatedModel}";
            $relationshipMethod = $this->buildRelationshipMethod($relationshipType, $relatedModel);

            $this->appendRelationship($modelPath, $relationshipMethod, $relatedModelClass);

            return [
                'status' => 'success',
                'message' => "Relationship added successfully to {$model}."
            ];
        }

        // If no relationship type or related model is provided, handle accordingly
        return [
            'status' => 'success',
            'message' => "No relationship added to {$model}. Either relationship type or related model is missing."
        ];
    }

    protected function buildRelationshipMethod($relationshipType, $relatedModel)
    {

        // Determine the method name based on the relationship type
        $methodName = $this->getMethodName($relationshipType, $relatedModel);

        $relationshipClass = $this->getRelationshipClass($relationshipType);

        $methodBody = match ($relationshipType) {
            'hasOne' => "return \$this->hasOne({$relatedModel}::class);",
            'hasMany' => "return \$this->hasMany({$relatedModel}::class);",
            'belongsTo' => "return \$this->belongsTo({$relatedModel}::class);",
            'belongsToMany' => "return \$this->belongsToMany({$relatedModel}::class);",
            'morphOne' => "return \$this->morphOne({$relatedModel}::class);",
            'morphMany' => "return \$this->morphMany({$relatedModel}::class);",
            'morphTo' => "return \$this->morphTo();",
            default => "return \$this->{$relationshipType}({$relatedModel}::class);",
        };

        return <<<EOD
        public function {$methodName}(): {$relationshipClass}
            {
                {$methodBody}
            }
        
        EOD;
    }

    protected function getMethodName($relationshipType, $relatedModel)
    {
        return match ($relationshipType) {
            'hasMany', 'belongsToMany', 'morphMany' => Str::camel(Str::plural($relatedModel,2)),
            'belongsTo' => Str::camel(Str::singular($relatedModel)),
            default => Str::camel(Str::singular($relatedModel)),
        };
    }

    protected function getRelationshipClass($relationshipType)
    {
        return match ($relationshipType) {
            'hasOne' => 'HasOne',
            'hasMany' => 'HasMany',
            'belongsTo' => 'BelongsTo',
            'belongsToMany' => 'BelongsToMany',
            'morphOne' => 'MorphOne',
            'morphMany' => 'MorphMany',
            'morphTo' => 'MorphTo',
            default => 'Relation',
        };
    }

    protected function appendRelationship($modelPath, $relationshipMethod, $relatedModelClass)
    {
        $modelContent = $this->files->get($modelPath);

        // Extract the namespace
        preg_match('/namespace\s+(.*?);/', $modelContent, $namespaceMatches);
        $namespace = $namespaceMatches[1] ?? 'App\Models';

        // Determine the relationship class and related model name
        $relationshipType = $this->getRelationshipTypeFromMethod($relationshipMethod);
        $relationshipClass = $this->getRelationshipClass($relationshipType);
        $useStatement = "use Illuminate\\Database\\Eloquent\\Relations\\{$relationshipClass};";

        // Ensure there is one blank line after the namespace declaration
        $modelContent = preg_replace('/(namespace\s+.*?;)\s*/', "$1\n\n", $modelContent);

        // Check if the use statement already exists to avoid duplication
        if (strpos($modelContent, $useStatement) === false) {
            // Find the position to insert the use statement (right after the namespace declaration)
            $namespaceEndPosition = strpos($modelContent, ";", strpos($modelContent, "namespace")) + 1;

            // Insert the use statement with a single newline after the namespace
            $modelContent = substr_replace($modelContent, "\n{$useStatement}", $namespaceEndPosition + 1, 0);
        }

        // Remove any extra blank lines after the use statements
        $modelContent = preg_replace("/(\n){3,}/", "\n\n", $modelContent);

        // Append the relationship method at the end of the class
        $modelContent = preg_replace(
            '/}\s*$/',
            "\n    {$relationshipMethod}\n}",
            $modelContent
        );

        // Save the updated content back to the model file
        $this->files->put($modelPath, $modelContent);
    }


    protected function getRelationshipTypeFromMethod($relationshipMethod)
    {
        if (strpos($relationshipMethod, 'hasOne') !== false) return 'hasOne';
        if (strpos($relationshipMethod, 'hasMany') !== false) return 'hasMany';
        if (strpos($relationshipMethod, 'belongsTo') !== false) return 'belongsTo';
        if (strpos($relationshipMethod, 'belongsToMany') !== false) return 'belongsToMany';
        if (strpos($relationshipMethod, 'morphOne') !== false) return 'morphOne';
        if (strpos($relationshipMethod, 'morphMany') !== false) return 'morphMany';
        if (strpos($relationshipMethod, 'morphTo') !== false) return 'morphTo';
        return 'relation';
    }
}