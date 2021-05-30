<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Tests\Fixtures\Resources;

use DigitalCreative\Jaqen\Services\Fields\Fields\EditableField;
use DigitalCreative\Jaqen\Services\ResourceManager\AbstractResource;
use DigitalCreative\Jaqen\Tests\Fixtures\Models\User as UserModel;

class MinimalUserResource extends AbstractResource
{

    public static string $model = UserModel::class;

    public function fields(): array
    {
        return [
            EditableField::make('Name'),
        ];
    }

}
