<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class AdvanceUsersSeed extends Command
{
    protected $signature = 'advance:users-seed';
    protected $description = 'Add softDeletes to users table, create UserSeeder, and seed the database';

    public function handle()
    {
        // Step 1: Check if already softdelete added & seeded
        $seedPath = database_path('seeders/UserSeeder.php');

        if (File::exists($seedPath)) {
            $this->error('Your User model already setup with Soft Delete & Seeder');
            return Command::FAILURE;
        }

        // Step 2: Create a new migration for adding softDeletes to users table
        /*$this->info('Creating migration for softDeletes...');
        Artisan::call('make:migration add_soft_deletes_to_users_table --table=users');
        $migrationPath = $this->getLatestMigrationFile();
        
        if ($migrationPath) {
            $migrationContent = File::get($migrationPath);
            $migrationContent = str_replace(
                'Schema::table(\'users\', function (Blueprint $table) {
            //',
                'Schema::table(\'users\', function (Blueprint $table) {
                $table->softDeletes();',
                $migrationContent
            );
            File::put($migrationPath, $migrationContent);
            $this->info('Migration file updated with softDeletes.');
        } else {
            $this->error('Failed to find or update the migration file.');
            return Command::FAILURE;
        }

        // Step 3: Run the migration
        $this->info('Running migration...');
        Artisan::call('migrate');
        $this->info('Migration completed.');*/

        // Step 4: Create UserSeeder
        $this->info('Creating UserSeeder...');
        $seederPath = database_path('seeders/UserSeeder.php');
        $seederContent = $this->getUserSeederContent();
        File::put($seederPath, $seederContent);
        $this->info('UserSeeder created successfully.');

        // Step 5: Run the seeder
        $this->info('Running UserSeeder...');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\UserSeeder']);
        $this->info('UserSeeder executed successfully.');

        return Command::SUCCESS;
    }

    private function getLatestMigrationFile()
    {
        $migrationFiles = glob(database_path('migrations/*_add_soft_deletes_to_users_table.php'));
        return end($migrationFiles);
    }

    private function getUserSeederContent()
    {
        return <<<EOT
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \$this->command->warn(PHP_EOL . 'Creating New Permissions...');
        \$permission1 = Permission::create(['name' => 'userview']);
        \$permission2 = Permission::create(['name' => 'usercreate']);
        \$permission3 = Permission::create(['name' => 'userupdate']);
        \$permission4 = Permission::create(['name' => 'userdelete']);
        \$permission5 = Permission::create(['name' => 'roleview']);
        \$permission6 = Permission::create(['name' => 'rolecreate']);
        \$permission7 = Permission::create(['name' => 'roleupdate']);
        \$permission8 = Permission::create(['name' => 'roledelete']);
        \$permission9 = Permission::create(['name' => 'permissionview']);
        \$permission10 = Permission::create(['name' => 'permissioncreate']);
        \$permission11 = Permission::create(['name' => 'permissionupdate']);
        \$permission12 = Permission::create(['name' => 'permissiondelete']);
        \$this->command->info('Permissions created.');

        \$this->command->warn(PHP_EOL . 'Creating New Role...');
        \$sadminRole = Role::create(['name' => 'Superadmin']);
        \$userRole = Role::create(['name' => 'User']);
        \$this->command->info('Role created.');

        \$this->command->warn(PHP_EOL . 'Giving Permissions to Role...');
        \$sadminRole->givePermissionTo(Permission::all());
        \$this->command->info('Permissions Given.');

        \$this->command->warn(PHP_EOL . 'Creating Super Admin user...');
        \$sadminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'name'  =>  'Super Admin',
            'password' => bcrypt('admin123'),
        ]);
        \$this->command->info('Super Admin user created.');

        \$this->command->warn(PHP_EOL . 'Adding some fake users...');
        \$users = User::factory(10)->create();
        \$this->command->info('User Added');

        \$this->command->warn(PHP_EOL . 'Assigning Roles...');
        \$sadminUser->assignRole(\$sadminRole);
        foreach(\$users as \$user){
            \$user->assignRole(\$userRole);
        }
        \$this->command->info('Role Assigned.');
    }
}
EOT;
    }
}