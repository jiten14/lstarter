<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateLayout extends Command
{
    protected $signature = 'generate:layout';

    protected $description = 'Copy layout.blade.php to the resources/views/layouts folder';

    public function handle()
    {
        $sourcePath = __DIR__ . '/../Layouts/layout.blade.php';
        $destinationPath = resource_path('views/layouts/layout.blade.php');

        $destinationDir = dirname($destinationPath);
        if (!File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        if (File::copy($sourcePath, $destinationPath)) {
            $this->info('layout.blade.php has been copied successfully.');
        } else {
            $this->error('Failed to copy layout.blade.php. Please check the source path.');
        }
    }
}
