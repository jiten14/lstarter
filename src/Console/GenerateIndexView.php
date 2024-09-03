<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\ViewIndexGenerator;

class GenerateIndexView extends Command
{
    protected $signature = 'generate:view-index {model}';
    protected $description = 'Generate a view folder and index.blade.php file for the given model';

    protected $viewGenerator;

    public function __construct(ViewIndexGenerator $viewGenerator)
    {
        parent::__construct();
        $this->viewGenerator = $viewGenerator;
    }

    public function handle()
    {
        $name = $this->argument('model');
        $result = $this->viewGenerator->generate($name);

        if ($result['status'] === 'error') {
            $this->error($result['message']);
        } else {
            $this->info($result['message']);
        }
    }
}