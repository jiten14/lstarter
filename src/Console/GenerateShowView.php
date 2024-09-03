<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\ViewShowGenerator;

class GenerateShowView extends Command
{
    protected $signature = 'generate:view-show {model}';
    protected $description = 'Generate a view folder and show.blade.php file for the given model';

    protected $viewGenerator;

    public function __construct(ViewShowGenerator $viewGenerator)
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