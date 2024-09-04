<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdvancePermission extends Command
{
    protected $signature = 'advance:permission';

    protected $description = 'Setup or update PermissionResource and related pages for Filament authentication';

    public function handle()
    {
        $permissionResourcePath = app_path('Filament/Resources/PermissionResource.php');

        if (!File::exists($permissionResourcePath)) {
            $this->info('PermissionResource does not exist. Creating it now...');
            Artisan::call('make:filament-resource Permission --generate');
            $this->info('PermissionResource created successfully.');
        }

        $content = File::get($permissionResourcePath);

        if (strpos($content, "protected static ?string \$navigationGroup = 'Auth';") !== false) {
            $this->error('Your permission is already setup');
            return Command::FAILURE;
        }

        $newContent = '<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationGroup = \'Auth\';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = \'heroicon-s-finger-print\';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(\'name\')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(\'name\')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            \'index\' => Pages\ListPermissions::route(\'/\'),
            \'create\' => Pages\CreatePermission::route(\'/create\'),
            \'edit\' => Pages\EditPermission::route(\'/{record}/edit\'),
        ];
    }
}';

        File::put($permissionResourcePath, $newContent);
        $this->info('PermissionResource updated successfully.');

        // Update CreatePermission.php
        $createPermissionPath = app_path('Filament/Resources/PermissionResource/Pages/CreatePermission.php');
        $createPermissionContent = '<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl(\'index\');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return \'Permission Created\';
    }
}';

        File::put($createPermissionPath, $createPermissionContent);
        $this->info('CreatePermission page updated successfully.');

        // Update EditPermission.php
        $editPermissionPath = app_path('Filament/Resources/PermissionResource/Pages/EditPermission.php');
        $editPermissionContent = '<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl(\'index\');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return \'Permission updated\';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}';

        File::put($editPermissionPath, $editPermissionContent);
        $this->info('EditPermission page updated successfully.');

        return Command::SUCCESS;
    }
}