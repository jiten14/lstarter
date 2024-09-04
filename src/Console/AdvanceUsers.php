<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdvanceUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'advance:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if Filament 3.2 and Spatie Permission are installed. Modify Role, Permission, and User models accordingly.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if the Filament package is installed
        if (!class_exists(\Filament\FilamentServiceProvider::class)) {
            $this->error('Install the required package first: Filament 3.2');
            return Command::FAILURE;
        }

        // Check if the Spatie Permission package is installed
        if (!class_exists(\Spatie\Permission\PermissionServiceProvider::class)) {
            $this->error('Install the required package first: Spatie Permission');
            return Command::FAILURE;
        }

        // Check if the User model exists in the app/Models directory
        if (!File::exists(app_path('Models/User.php'))) {
            $this->error('User model does not exist. Please create the User model first.');
            return Command::FAILURE;
        }

        // Check if FilamentUser is already imported in the User model
        $userModelContent = File::get(app_path('Models/User.php'));
        if (strpos($userModelContent, 'use Filament\Models\Contracts\FilamentUser;') !== false) {
            $this->error('Your user model is already setup');
            return Command::FAILURE;
        }

        // Proceed with modifying the User model
        $userModelPath = app_path('Models/User.php');

        // Insert necessary uses after Notifiable
        $useSearch = 'use Illuminate\Notifications\Notifiable;';
        $useReplace = $useSearch . "\n" . 'use Filament\Panel;' . "\n" .
                      'use Filament\Models\Contracts\FilamentUser;' . "\n" .
                      'use App\Models\Role;' . "\n" .
                      'use Spatie\Permission\Traits\HasRoles;' . "\n" .
                      'use Illuminate\Database\Eloquent\SoftDeletes;';
        $userModelContent = str_replace($useSearch, $useReplace, $userModelContent);

        // Replace extends Authenticatable with FilamentUser interface
        $extendsSearch = 'extends Authenticatable';
        $extendsReplace = 'extends Authenticatable implements FilamentUser';
        $userModelContent = str_replace($extendsSearch, $extendsReplace, $userModelContent);

        // Replace HasFactory, Notifiable with additional traits
        $traitsSearch = 'use HasFactory, Notifiable;';
        $traitsReplace = 'use HasFactory, Notifiable, HasRoles, SoftDeletes;';
        $userModelContent = str_replace($traitsSearch, $traitsReplace, $userModelContent);

        // Add canAccessPanel method at the end of the User class
        $canAccessPanelMethod = "\n" . '    public function canAccessPanel(Panel $panel): bool' . "\n" .
                                '    {' . "\n" .
                                '        return $this->hasAnyRole(Role::all());' . "\n" .
                                '    }' . "\n";
        $lastBracePosition = strrpos($userModelContent, '}');
        $userModelContent = substr_replace($userModelContent, $canAccessPanelMethod . '}', $lastBracePosition);

        // Save the modified content back to the User model file
        File::put($userModelPath, $userModelContent);
        $this->info('User model updated successfully.');

        // Create and modify Role model
        if (!File::exists(app_path('Models/Role.php'))) {
            $this->call('make:model', ['name' => 'Role']);
            $this->info('Role model created successfully in app/Models.');

            $roleModelPath = app_path('Models/Role.php');
            $roleModelContent = File::get($roleModelPath);

            $roleModelContent = str_replace(
                'use Illuminate\Database\Eloquent\Model;',
                'use Spatie\Permission\Models\Role as ModelsRole;',
                $roleModelContent
            );

            $roleModelContent = str_replace(
                'extends Model',
                'extends ModelsRole',
                $roleModelContent
            );

            File::put($roleModelPath, $roleModelContent);
            $this->info('Role model updated successfully.');
        }

        // Create and modify Permission model
        if (!File::exists(app_path('Models/Permission.php'))) {
            $this->call('make:model', ['name' => 'Permission']);
            $this->info('Permission model created successfully in app/Models.');

            $permissionModelPath = app_path('Models/Permission.php');
            $permissionModelContent = File::get($permissionModelPath);

            $permissionModelContent = str_replace(
                'use Illuminate\Database\Eloquent\Model;',
                'use Spatie\Permission\Models\Permission as ModelsPermission;',
                $permissionModelContent
            );

            $permissionModelContent = str_replace(
                'extends Model',
                'extends ModelsPermission',
                $permissionModelContent
            );

            File::put($permissionModelPath, $permissionModelContent);
            $this->info('Permission model updated successfully.');
        }

        return Command::SUCCESS;
    }
}