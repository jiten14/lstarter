<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdvanceAuth extends Command
{
    protected $signature = 'advance:auth';

    protected $description = 'Setup or update UserResource and related pages for Filament authentication';

    public function handle()
    {
        $userResourcePath = app_path('Filament/Resources/UserResource.php');

        if (!File::exists($userResourcePath)) {
            $this->info('UserResource does not exist. Creating it now...');
            Artisan::call('make:filament-resource User --soft-deletes --generate');
            $this->info('UserResource created successfully.');
        }

        $content = File::get($userResourcePath);

        if (strpos($content, "protected static ?string \$navigationGroup = 'Auth';") !== false) {
            $this->error('Your auth is already setup');
            return Command::FAILURE;
        }

        $newContent = '<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = \'Auth\';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = \'heroicon-s-users\';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make(\'name\')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make(\'email\')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make(\'password\')
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === \'create\'),
                Forms\Components\CheckboxList::make(\'role\')
                    ->relationship(\'Roles\', \'name\')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(\'name\')
                    ->searchable(),
                Tables\Columns\TextColumn::make(\'email\')
                    ->searchable(),
                Tables\Columns\TextColumn::make(\'roles.name\')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            \'index\' => Pages\ListUsers::route(\'/\'),
            \'create\' => Pages\CreateUser::route(\'/create\'),
            \'edit\' => Pages\EditUser::route(\'/{record}/edit\'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}';

        File::put($userResourcePath, $newContent);
        $this->info('UserResource updated successfully.');

        // Update CreateUser.php
        $createUserPath = app_path('Filament/Resources/UserResource/Pages/CreateUser.php');
        $createUserContent = '<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl(\'index\');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return \'User registered\';
    }
}';

        File::put($createUserPath, $createUserContent);
        $this->info('CreateUser page updated successfully.');

        // Update EditUser.php
        $editUserPath = app_path('Filament/Resources/UserResource/Pages/EditUser.php');
        $editUserContent = '<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl(\'index\');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return \'User updated\';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}';

        File::put($editUserPath, $editUserContent);
        $this->info('EditUser page updated successfully.');

        return Command::SUCCESS;
    }
}