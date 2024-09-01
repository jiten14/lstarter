<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\ControllerGenerator;

class GenerateController extends Command
{
    protected $signature = 'generate:controller {model}';
    protected $description = 'Generate a resource controller with prefilled data from the model';

    protected $controllerGenerator;

    public function __construct(ControllerGenerator $controllerGenerator)
    {
        parent::__construct();
        $this->controllerGenerator = $controllerGenerator;
    }

    public function handle()
    {
        $name = $this->argument('model');
        $result = $this->controllerGenerator->generate($name);

        if ($result['status'] === 'error') {
            $this->error($result['message']);
        } else {
            $this->info($result['message']);
        }
    }
}