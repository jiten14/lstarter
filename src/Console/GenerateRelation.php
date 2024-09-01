<?php

namespace Jiten14\Lstarter\Console;

use Illuminate\Console\Command;
use Jiten14\Lstarter\Generator\RelationGenerator;

class GenerateRelation extends Command
{
    protected $signature = 'generate:relation {model}';
    protected $description = 'Add relationships to an existing model';

    protected $relationGenerator;

    public function __construct(RelationGenerator $relationGenerator)
    {
        parent::__construct();
        $this->relationGenerator = $relationGenerator;
    }

    public function handle()
    {
        $model = $this->argument('model');
        $relationshipType = $this->choice(
            'Select the relationship type',
            ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany', 'morphOne', 'morphMany', 'morphTo'],
            0
        );

        $relatedModel = $this->ask('What is the related model name?');

        $result = $this->relationGenerator->generate($model, $relationshipType, $relatedModel);

        if ($result['status'] === 'error') {
            $this->error($result['message']);
        } else {
            $this->info($result['message']);
        }
    }
}
