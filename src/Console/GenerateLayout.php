<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\LayoutGenerator;

class GenerateLayout extends Command
{
    protected $signature = 'generate:layout';
    protected $description = 'Generate the layout file for the application';

    protected $layoutGenerator;

    public function __construct(LayoutGenerator $layoutGenerator)
    {
        parent::__construct();
        $this->layoutGenerator = $layoutGenerator;
    }

    public function handle()
    {
        $result = $this->layoutGenerator->generate();

        switch ($result['status']) {
            case 'success':
                $this->info($result['message']);
                break;
            case 'warning':
                $this->warn($result['message']);
                break;
            case 'error':
                $this->error($result['message']);
                break;
        }
    }
}