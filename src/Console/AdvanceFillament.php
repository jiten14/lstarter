<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdvanceFillament extends Command
{
    protected $signature = 'advance:fillament';
    protected $description = 'Set up Filament admin panel with custom profile and color settings';

    public function handle()
    {
        $filePath = app_path('Providers/Filament/AdminPanelProvider.php');

        if (!File::exists($filePath)) {
            $this->error('Please install the Filament panel first.');
            return Command::FAILURE;
        }

        $content = File::get($filePath);

        if (strpos($content, '->profile(isSimple: false)') !== false) {
            $this->error('Your panel is already set up.');
            return Command::FAILURE;
        }

        // Add profile and dark mode settings
        $content = preg_replace(
            '/->login\(\)/',
            "->login()\n            ->profile(isSimple: false)\n            ->darkMode(false)",
            $content
        );

        // Update color settings
        $content = str_replace(
            "->colors([
                'primary' => Color::Amber,
            ])",
            "->colors([
                //'primary' => Color::Amber,
                'primary' => Color::Green,
            ])",
            $content
        );

        File::put($filePath, $content);

        $this->info('Filament admin panel has been successfully updated.');
        return Command::SUCCESS;
    }
}