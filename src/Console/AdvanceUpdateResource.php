<?php
namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdvanceUpdateResource extends Command
{
    protected $signature = 'advance:update-resource {model}';

    protected $description = 'Update Fillament Resource with custom actions and navigation group';

    public function handle()
    {
        $modelName = ucfirst($this->argument('model'));
        $resourcePath = app_path("Filament/Resources/{$modelName}Resource.php");

        if (!File::exists($resourcePath)) {
            $this->error("First create the Resource using Fillament Command for the model: {$modelName}");
            return Command::FAILURE;
        }

        $resourceContent = File::get($resourcePath);

        if (str_contains($resourceContent, 'protected static ?string $navigationGroup')) {
            $this->error("Your resource is already updated.");
            return Command::FAILURE;
        }

        $updatedContent = str_replace(
            'protected static ?string $navigationIcon = \'heroicon-o-rectangle-stack\';',
            "protected static ?string \$navigationGroup = 'Database';
            protected static ?int \$navigationSort = 1;
            protected static ?string \$navigationIcon = 'heroicon-s-circle-stack';",
            $resourceContent
        );

        $updatedContent = str_replace(
            'Tables\Actions\EditAction::make(),',
            "Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
            Tables\Actions\RestoreAction::make(),",
            $updatedContent
        );

        File::put($resourcePath, $updatedContent);
        $this->info("Resource '{$modelName}Resource' updated successfully.");

        $createPagePath = app_path("Filament/Resources/{$modelName}Resource/Pages/Create{$modelName}.php");
        $createPageContent = File::get($createPagePath);

        $updatedCreatePageContent = str_replace(
            "protected static string \$resource = {$modelName}Resource::class;",
            "protected static string \$resource = {$modelName}Resource::class;
            protected static bool \$canCreateAnother = false;
            protected function getRedirectUrl(): string
            {
                return \$this->previousUrl ?? \$this->getResource()::getUrl('index');
            }
            protected function getCreatedNotificationTitle(): ?string
            {
                return '{$modelName} Created';
            }"
        , $createPageContent);

        File::put($createPagePath, $updatedCreatePageContent);
        $this->info("Create{$modelName} page updated successfully.");

        $editPagePath = app_path("Filament/Resources/{$modelName}Resource/Pages/Edit{$modelName}.php");
        $editPageContent = File::get($editPagePath);

        $updatedEditPageContent = str_replace(
            "protected static string \$resource = {$modelName}Resource::class;",
            "protected static string \$resource = {$modelName}Resource::class;
            protected function getRedirectUrl(): string
            {
                return \$this->previousUrl ?? \$this->getResource()::getUrl('index');
            }
            protected function getSavedNotificationTitle(): ?string
            {
                return '{$modelName} updated';
            }"
        , $editPageContent);

        File::put($editPagePath, $updatedEditPageContent);
        $this->info("Edit{$modelName} page updated successfully.");

        return Command::SUCCESS;
    }
}