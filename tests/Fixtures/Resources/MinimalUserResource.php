<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Tests\Fixtures\Resources;

use DigitalCreative\Jaqen\Fields\EditableField;
use DigitalCreative\Jaqen\Resources\AbstractResource;
use DigitalCreative\Jaqen\Tests\Fixtures\Models\User as UserModel;
use Illuminate\Database\Eloquent\Model;

class MinimalUserResource extends AbstractResource
{

    public function model(): Model
    {
        return new UserModel();
    }

    public function fields(): array
    {
        return [
            EditableField::make('Name'),
        ];
    }

}
