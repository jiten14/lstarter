<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\ModelGenerator;

class GenerateModel extends Command
{
    protected $signature = 'generate:model {name}';
    protected $description = 'Generate a model from an existing migration table';

    protected $modelGenerator;

    public function __construct(ModelGenerator $modelGenerator)
    {
        parent::__construct();
        $this->modelGenerator = $modelGenerator;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $result = $this->modelGenerator->generate($name);

        if ($result['status'] === 'error') {
            $this->error($result['message']);
        } else {
            $this->info($result['message']);
        }
    }
}
