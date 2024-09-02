<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MigrationGenerator
{
    /**
     * Generate a new migration file.
     *
     * @param string $tableName
     * @param string $fields
     * @param string $modifiers
     * @param string $relations
     * @return void
     */
    public function generate(string $tableName, string $fields, string $modifiers, string $relations, bool $softDeletes)
    {
        // Prepare migration file name
        $fileName = date('Y_m_d_His') . '_create_' . Str::lower(Str::plural($tableName)) . '_table.php';

        // Get migration template content
        $templatePath = __DIR__ . '/../Templates/migration.stub';
        $templateContent = File::get($templatePath);

        // Replace placeholders in the template
        $migrationContent = $this->replacePlaceholders($templateContent, $tableName, $fields, $modifiers, $relations, $softDeletes);

        // Define the destination path
        $destinationPath = database_path('migrations/' . $fileName);

        // Write the migration file
        File::put($destinationPath, $migrationContent);
    }

    /**
     * Replace placeholders in the migration template.
     *
     * @param string $template
     * @param string $tableName
     * @param string $fields
     * @param string $modifiers
     * @param string $relations
     * @return string
     */
    private function replacePlaceholders(string $template, string $tableName, string $fields, string $modifiers, string $relations, bool $softDeletes): string
    {
        $className = 'Create' . Str::lower(Str::studly(Str::plural($tableName))) . 'Table';
        $tableNamePlural = Str::lower(Str::plural($tableName));
        $fieldDefinitions = $this->generateFieldDefinitions($fields, $modifiers);
        $relationDefinitions = $this->generateRelationDefinitions($relations);

        $softDeletesDefinition = $softDeletes ? "\n            \$table->softDeletes();" : '';

        $combinedDefinitions = "\$table->id();" .
                            ($relationDefinitions ? "\n            " . $relationDefinitions : '') .
                            ($fieldDefinitions ? "\n            " . $fieldDefinitions : '') .
                            $softDeletesDefinition;

        return str_replace(
            ['{{className}}', '{{tableName}}', '{{fieldDefinitions}}'],
            [$className, $tableNamePlural, $combinedDefinitions],
            $template
        );
    }

    /**
     * Generate field definitions for the migration.
     *
     * @param string $fields
     * @param string $modifiers
     * @return string
     */
    private function generateFieldDefinitions(string $fields, string $modifiers): string
    {
        $definitions = [];
        $fieldArray = explode(',', $fields);
        $modifierArray = $this->parseModifiers($modifiers);

        foreach ($fieldArray as $field) {
            $parts = explode(':', trim($field));
            if (count($parts) === 2) {
                $fieldName = trim($parts[0]);
                $fieldType = trim($parts[1]);
                $definition = "\$table->{$fieldType}('{$fieldName}')";

                if (isset($modifierArray[$fieldName])) {
                    $definition .= $modifierArray[$fieldName];
                }

                $definitions[] = $definition . ';';
            }
        }

        return implode("\n            ", $definitions);
    }

    /**
     * Generate relation definitions for the migration.
     *
     * @param string $relations
     * @return string
     */
    private function generateRelationDefinitions(string $relations): string
    {
        if (empty(trim($relations))) {
            return '';
        }

        $definitions = [];
        $relationArray = explode(',', $relations);

        foreach ($relationArray as $relation) {
            $parts = explode(':', trim($relation));
            if (count($parts) === 2) {
                $fieldName = trim($parts[0]);
                $referencedTable = trim($parts[1]);

                $definitions[] = "\$table->foreignId('{$fieldName}')->constrained('{$referencedTable}')->cascadeOnDelete();";
            }
        }

        return implode("\n            ", $definitions);
    }

    /**
     * Parse modifiers into an associative array.
     *
     * @param string $modifiers
     * @return array
     */
    private function parseModifiers(string $modifiers): array
    {
        $modifierArray = [];
        $parts = explode(',', $modifiers);

        foreach ($parts as $part) {
            $modifierParts = explode(':', trim($part));
            if (count($modifierParts) === 2) {
                $fieldName = trim($modifierParts[0]);
                $modifier = trim($modifierParts[1]);

                // Ensure correct syntax without extra parentheses
                if (strpos($modifier, '(') === false) {
                    $modifierArray[$fieldName] = "->{$modifier}()";
                } else {
                    $modifierArray[$fieldName] = "->{$modifier}";
                }
            }
        }

        return $modifierArray;
    }
}
