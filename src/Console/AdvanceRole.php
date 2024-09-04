<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdvanceRole extends Command
{
    protected $signature = 'advance:role';

    protected $description = 'Setup or update RoleResource and related pages for Filament authentication';

    public function handle()
    {
        $roleResourcePath = app_path('Filament/Resources/RoleResource.php');

        if (!File::exists($roleResourcePath)) {
            $this->info('RoleResource does not exist. Creating it now...');
            Artisan::call('make:filament-resource Role --generate');
            $this->info('RoleResource created successfully.');
        }

        $content = File::get($roleResourcePath);

        if (strpos($content, "protected static ?string \$navigationGroup = 'Auth';") !== false) {
            $this->error('Your role is already setup');
            return Command::FAILURE;
        }

        $newContent = '<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = \'Auth\';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = \'heroicon-s-arrow-path\';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(\'name\')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\CheckboxList::make(\'permission\')
                    ->relationship(
                        name: \'Permissions\',
                        titleAttribute: \'name\',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy(\'name\',\'desc\'),
                    )
                    ->bulkToggleable()
                    ->columns(4)
                    ->gridDirection(\'row\')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(\'name\')
                    ->searchable(),
                Tables\Columns\TextColumn::make(\'permissions.name\')
                    ->wrap(),
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
            \'index\' => Pages\ListRoles::route(\'/\'),
            \'create\' => Pages\CreateRole::route(\'/create\'),
            \'edit\' => Pages\EditRole::route(\'/{record}/edit\'),
        ];
    }
}';

        File::put($roleResourcePath, $newContent);
        $this->info('RoleResource updated successfully.');

        // Update CreateRole.php
        $createRolePath = app_path('Filament/Resources/RoleResource/Pages/CreateRole.php');
        $createRoleContent = '<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl(\'index\');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return \'Role Created\';
    }
}';

        File::put($createRolePath, $createRoleContent);
        $this->info('CreateRole page updated successfully.');

        // Update EditRole.php
        $editRolePath = app_path('Filament/Resources/RoleResource/Pages/EditRole.php');
        $editRoleContent = '<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl(\'index\');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return \'Role updated\';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}';

        File::put($editRolePath, $editRoleContent);
        $this->info('EditRole page updated successfully.');

        return Command::SUCCESS;
    }
}