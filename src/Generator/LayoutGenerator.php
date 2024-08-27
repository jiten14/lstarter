<?php

namespace Jiten14\Lstarter\Generator;

use Illuminate\Support\Facades\File;

class LayoutGenerator
{
    /**
     * Generate the layout by copying it from the package to the Laravel project.
     * This method is called from the GenerateLayout's handle method.
     */
    public function generate()
    {
        // Define the source path (location within your package)
        $sourcePath = __DIR__ . '/../Layouts/layout.blade.php';

        // Define the destination path (location within the Laravel app)
        $destinationPath = resource_path('views/layouts/layout.blade.php');

        // Ensure the destination directory exists
        if (!File::isDirectory(dirname($destinationPath))) {
            File::makeDirectory(dirname($destinationPath), 0755, true);
        }

        // Copy the layout file from the source to the destination
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destinationPath);
        } else {
            // Handle the case where the layout file does not exist
            throw new \Exception("Layout file not found at: $sourcePath");
        }
    }
}
