<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FillamentSetup extends Command
{
    protected $signature = 'lstarter:filla-setup';

    protected $description = 'Advance Fillament Setup with Role & Permission';

    public function handle()
    {
        // Step 1: Setup User Model
        $this->info('Setting up user model for Fillament');
        $this->call('advance:users');
        // Step 2: User Table Soft Delete Migration & seeder
        //$this->info('Adding soft delete to users table & Seeding data');
        //$this->call('advance:users-seed');
        // Step 3: Setting off User,Role & Permission Resources
        $this->info('Setting off User,Role & Permission Resources');
        $this->call('advance:auth');
        $this->call('advance:role');
        $this->call('advance:permission');
        // Step 4: Setting off Authorization Policy
        $this->info('Setting off Authorization Policy');
        $this->call('advance:policy');
        // Step 5: Setting off Fillament Panel
        $this->info('Setting off Fillament Panel');
        $this->call('advance:fillament');

        $this->info('Fillament setup completed successfully!');

    }
}