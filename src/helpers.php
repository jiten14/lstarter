<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

if (!function_exists('generateMenuItems')) {
    function generateMenuItems()
    {
        $viewPath = resource_path('views');
        $folders = File::directories($viewPath);

        $menuItems = [];

        foreach ($folders as $folder) {
            $folderName = basename($folder);
            $modelName = Str::singular(Str::studly($folderName));

            // Check if a route exists for this model
            if (Route::has($folderName . '.index')) {
                $menuItems[] = [
                    'name' => $folderName,
                    'url' => route($folderName . '.index'),
                ];
            }
        }

        return $menuItems;
    }
}
