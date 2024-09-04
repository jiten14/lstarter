<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdvancePolicy extends Command
{
    protected $signature = 'advance:policy';

    protected $description = 'Setup or update User, Role, and Permission policies';

    public function handle()
    {
        $policies = [
            'User' => 'UserPolicy',
            'Role' => 'RolePolicy',
            'Permission' => 'PermissionPolicy',
        ];

        foreach ($policies as $model => $policy) {
            $policyPath = app_path("Policies/{$policy}.php");

            if (!File::exists($policyPath)) {
                $this->info("{$policy} does not exist. Creating it now...");
                Artisan::call("make:policy {$policy} --model={$model}");
                $this->info("{$policy} created successfully.");
            }

            $this->updatePolicyContent($policy, $policyPath);
        }

        return Command::SUCCESS;
    }

    private function updatePolicyContent($policy, $policyPath)
    {
        $content = $this->getPolicyContent($policy);
        File::put($policyPath, $content);
        $this->info("{$policy} updated successfully.");
    }

    private function getPolicyContent($policy)
    {
        switch ($policy) {
            case 'UserPolicy':
                return '<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([\'userview\',\'usercreate\',\'userupdate\',\'userdelete\']);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasAnyPermission([\'userview\',\'usercreate\',\'userupdate\',\'userdelete\']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(\'usercreate\');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo(\'userupdate\');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo(\'userdelete\');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo(\'userdelete\');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo(\'userdelete\');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo(\'userdelete\');
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasPermissionTo(\'userdelete\');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo(\'userdelete\');
    }
}';

            case 'RolePolicy':
                return '<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([\'roleview\',\'rolecreate\',\'roleupdate\',\'roledelete\']);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasAnyPermission([\'roleview\',\'rolecreate\',\'roleupdate\',\'roledelete\']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(\'rolecreate\');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(\'roleupdate\');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(\'roledelete\');
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(\'roledelete\');
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(\'roledelete\');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo(\'roledelete\');
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasPermissionTo(\'roledelete\');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo(\'roledelete\');
    }
}';

            case 'PermissionPolicy':
                return '<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([\'permissionview\',\'permissioncreate\',\'permissionupdate\',\'permissiondelete\']);
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasAnyPermission([\'permissionview\',\'permissioncreate\',\'permissionupdate\',\'permissiondelete\']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(\'permissioncreate\');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(\'permissionupdate\');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(\'permissiondelete\');
    }

    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(\'permissiondelete\');
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo(\'permissiondelete\');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo(\'permissiondelete\');
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasPermissionTo(\'permissiondelete\');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasPermissionTo(\'permissiondelete\');
    }
}';

            default:
                return '';
        }
    }
}