<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\FactoryGenerator;

class GenerateFactory extends Command
{
    protected $signature = 'generate:factory {model}';
    protected $description = 'Generate a factory and add it to the database seeder file';

    protected $factoryGenerator;

    public function __construct(FactoryGenerator $factoryGenerator)
    {
        parent::__construct();
        $this->factoryGenerator = $factoryGenerator;
    }

    public function handle()
    {
        $model = $this->argument('model');

        $result = $this->factoryGenerator->generate($model);

        if ($result['status'] === 'error') {
            $this->error($result['message']);
        } else {
            $this->info($result['message']);
        }
    }
}
