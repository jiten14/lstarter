<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\ViewEditGenerator;

class GenerateEditView extends Command
{
    protected $signature = 'generate:view-edit {model}';
    protected $description = 'Generate an edit view for the given model';

    protected $viewGenerator;

    public function __construct(ViewEditGenerator $viewGenerator)
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