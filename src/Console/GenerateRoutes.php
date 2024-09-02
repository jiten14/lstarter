<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class GenerateRoutes extends Command
{
    protected $signature = 'generate:routes {model}';
    protected $description = 'Generate routes for a resource controller';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $model = $this->argument('model');
        $modelName = Str::studly($model);
        $controllerName = "{$modelName}Controller";
        $modelNamePlural = Str::lower(Str::plural($model));

        $webPhpPath = base_path('routes/web.php');
        $content = $this->files->get($webPhpPath);

        // Check if the controller import statement already exists
        if (!Str::contains($content, "use App\Http\Controllers\\{$controllerName};")) {
            // Find the first occurrence of "use Illuminate\Support\Facades\Route;"
            $insertPosition = strpos($content, "use Illuminate\Support\Facades\Route;") + strlen("use Illuminate\Support\Facades\Route;");

            // Insert the controller import statement
            $content = substr_replace($content, "\nuse App\Http\Controllers\\{$controllerName};", $insertPosition, 0);
        }

        // Check if the route resource already exists
        if (!Str::contains($content, "Route::resource('{$modelNamePlural}', {$controllerName}::class);")) {
            // Add the route resource
            $content .= "\nRoute::resource('{$modelNamePlural}', {$controllerName}::class);";
            $this->files->put($webPhpPath, $content);
            $this->info("Routes for {$modelName} added to web.php successfully.");
        } else {
            $this->warn("Routes for {$modelName} already exist in web.php.");
        }
    }
}