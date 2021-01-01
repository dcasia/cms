<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Tests\Feature\Fields;

use DigitalCreative\Jaqen\Fields\EditableField;
use DigitalCreative\Jaqen\Tests\Factories\UserFactory;
use DigitalCreative\Jaqen\Tests\Fixtures\Models\User as UserModel;
use DigitalCreative\Jaqen\Tests\TestCase;
use DigitalCreative\Jaqen\Tests\Traits\RequestTrait;
use DigitalCreative\Jaqen\Tests\Traits\ResourceTrait;

class EditableFieldTest extends TestCase
{

    use RequestTrait;
    use ResourceTrait;

    public function test_editable_field_works(): void
    {

        $data = [
            'name' => 'test',
            'email' => 'email@email.com',
            'gender' => 'male',
            'password' => 123456,
        ];

        $resource = $this->makeResource()
                         ->addDefaultFields(
                             EditableField::make('Name')->rulesForCreate('required'),
                             EditableField::make('Email')->rulesForCreate('required'),
                             EditableField::make('Gender')->rulesForCreate('required'),
                             EditableField::make('Password')->rulesForCreate('required'),
                         );

        $this->storeResponse($resource, $data);

        $this->assertDatabaseHas('users', $data);

    }

    public function test_editable_field_on_update_works(): void
    {

        $user = UserFactory::new()->create();

        $resource = $this->makeResource(UserModel::class)
                         ->addDefaultFields(
                             new EditableField('Name'),
                             new EditableField('Email'),
                             new EditableField('Gender'),
                         );

        $this->updateResponse($resource, $user->id, [ 'name' => 'updated' ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
            'name' => 'updated',
        ]);

    }

}
