<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Tests\Fixtures\Resources;

use DigitalCreative\Jaqen\Services\Fields\Fields\EditableField;
use DigitalCreative\Jaqen\Services\ResourceManager\AbstractResource;
use DigitalCreative\Jaqen\Tests\Fixtures\Models\Phone as PhoneModel;
use Illuminate\Database\Eloquent\Model;

class PhoneResource extends AbstractResource
{

    public function model(): Model
    {
        return new PhoneModel();
    }

    public function fields(): array
    {
        return [
            EditableField::make('Number'),
        ];
    }

    public function fieldsForFieldsWithValidation(): array
    {
        return [
            EditableField::make('Number')->rules([ 'numeric' ]),
        ];
    }

}
