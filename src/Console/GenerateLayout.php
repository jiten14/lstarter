<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\LayoutGenerator;

class GenerateLayout extends Command
{
    // The name and signature of the console command
    protected $signature = 'generate:layout';

    // The console command description
    protected $description = 'Copy layout.blade.php to the resources/views/layouts folder';

    // The layout generator instance
    protected $layoutGenerator;

    // Constructor to initialize the layout generator
    public function __construct(LayoutGenerator $layoutGenerator)
    {
        parent::__construct();
        $this->layoutGenerator = $layoutGenerator;
    }

    public function handle()
    {
        // Call the generate method from LayoutGenerator
        $this->layoutGenerator->generate();

        // Output a success message
        $this->info('Layout generated successfully!');
        
    }
}
