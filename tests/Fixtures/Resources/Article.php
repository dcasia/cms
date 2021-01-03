<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Tests\Fixtures\Resources;

use DigitalCreative\Jaqen\Fields\Relationships\BelongsToField;
use DigitalCreative\Jaqen\Services\Fields\EditableField;
use DigitalCreative\Jaqen\Services\Fields\ReadOnlyField;
use DigitalCreative\Jaqen\Services\ResourceManager\AbstractResource;
use DigitalCreative\Jaqen\Tests\Fixtures\Models\Article as ArticleModel;
use Illuminate\Database\Eloquent\Model;

class Article extends AbstractResource
{

    public function model(): Model
    {
        return new ArticleModel();
    }

    public function fields(): array
    {
        return [
            ReadOnlyField::make('id'),
            EditableField::make('Title'),
            EditableField::make('Content'),
            BelongsToField::make('User', 'user', User::class),
        ];
    }

}
