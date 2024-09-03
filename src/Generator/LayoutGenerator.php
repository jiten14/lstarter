<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Facades\File;

class LayoutGenerator
{
    /**
     * Generate the layout by copying it from the package to the Laravel project.
     * This method is called from the GenerateLayout's handle method.
     *
     * @return array
     */
    public function generate()
    {
        // Define the source path (location within your package)
        $sourcePath = __DIR__ . '/../Layouts/layout.blade.php';

        // Define the destination path (location within the Laravel app)
        $destinationPath = resource_path('views/layouts/layout.blade.php');

        // Check if the source file exists
        if (!File::exists($sourcePath)) {
            return [
                'status' => 'error',
                'message' => "Layout file not found at: $sourcePath"
            ];
        }

        // Check if the destination file already exists
        if (File::exists($destinationPath)) {
            return [
                'status' => 'warning',
                'message' => "Layout file already exists at: $destinationPath"
            ];
        }

        // Ensure the destination directory exists
        if (!File::isDirectory(dirname($destinationPath))) {
            File::makeDirectory(dirname($destinationPath), 0755, true);
        }

        // Copy the layout file from the source to the destination
        File::copy($sourcePath, $destinationPath);

        return [
            'status' => 'success',
            'message' => "Layout file successfully copied to: $destinationPath"
        ];
    }
}