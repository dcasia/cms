<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Tests\Feature;

use DigitalCreative\Dashboard\Fields\EditableField;
use DigitalCreative\Dashboard\Http\Controllers\Resources\DeleteController;
use DigitalCreative\Dashboard\Http\Controllers\Resources\StoreController;
use DigitalCreative\Dashboard\Http\Controllers\Resources\UpdateController;
use DigitalCreative\Dashboard\Resources\AbstractResource;
use DigitalCreative\Dashboard\Tests\Factories\UserFactory;
use DigitalCreative\Dashboard\Tests\Fixtures\Models\User;
use DigitalCreative\Dashboard\Tests\Fixtures\Models\User as UserModel;
use DigitalCreative\Dashboard\Tests\TestCase;
use DigitalCreative\Dashboard\Tests\Traits\RequestTrait;
use DigitalCreative\Dashboard\Tests\Traits\ResourceTrait;
use Illuminate\Database\Eloquent\Model;

class ResourceEventTest extends TestCase
{

    use ResourceTrait;
    use RequestTrait;

    public function test_before_create_event_works(): void
    {

        $resource = $this->getPreConfiguredResource()
                         ->beforeCreate(function(array $data) {
                             $this->assertEquals([ 'name' => 'ignored' ], $data);
                             return [ 'name' => 'hello' ];
                         });

        $request = $this->storeRequest($resource, [ 'name' => 'ignored' ]);

        (new StoreController())->handle($request);

        $this->assertDatabaseHas('users', [ 'name' => 'hello' ]);

    }

    public function test_after_create_event_works(): void
    {

        $resource = $this->getPreConfiguredResource()
                         ->afterCreate(function(UserModel $model) {
                             $this->assertInstanceOf(UserModel::class, $model);
                             return [ 'success' => true ];
                         });

        $request = $this->storeRequest($resource, [ 'name' => 'ignored' ]);

        $response = (new StoreController())->handle($request)->getData(true);

        $this->assertEquals([ 'success' => true ], $response);

    }

    public function test_chaining_multiple_after_create_event_works(): void
    {

        $resource = $this->getPreConfiguredResource()
                         ->afterCreate(function(UserModel $model) {
                             $this->assertInstanceOf(UserModel::class, $model);
                             return [ 'success' => true ];
                         })
                         ->afterCreate(function(array $data) {
                             $this->assertEquals([ 'success' => true ], $data);
                             return array_merge($data, [ 'appended' => true ]);
                         });

        $request = $this->storeRequest($resource, [ 'name' => 'ignored' ]);

        $response = (new StoreController())->handle($request)->getData(true);

        $this->assertEquals([ 'success' => true, 'appended' => true ], $response);

    }

    public function test_before_update_event_works(): void
    {

        $user = UserFactory::new()->create();

        $resource = $this->getPreConfiguredResource()
                         ->beforeUpdate(function(UserModel $model, array $data) use ($user) {
                             $this->assertEquals($user->getKey(), $model->getKey());
                             $this->assertSame([ 'name' => 'ignored' ], $data);
                             return [ 'name' => 'hello' ];
                         });

        $request = $this->updateRequest($resource, $user->id, [ 'name' => 'ignored' ]);

        (new UpdateController())->handle($request);

        $this->assertDatabaseHas('users', [ 'name' => 'hello' ]);

    }

    public function test_after_update_event_works(): void
    {

        $user = UserFactory::new()->create();

        $resource = $this->getPreConfiguredResource()
                         ->afterUpdate(function(UserModel $model) use ($user) {
                             $this->assertEquals($user->getKey(), $model->getKey());
                         });

        $request = $this->updateRequest($resource, $user->id, [ 'name' => 'ignored' ]);

        (new UpdateController())->handle($request);

    }

    public function test_before_delete_event_works(): void
    {

        $user = UserFactory::new()->create();

        $resource = $this->getPreConfiguredResource()
                         ->beforeDelete(function(UserModel $model) use ($user) {
                             $this->assertEquals($user->getKey(), $model->getKey());
                         });

        $request = $this->deleteRequest($resource, [ $user->id ]);

        (new DeleteController())->handle($request);

    }

    public function test_after_delete_event_works(): void
    {

        $user = UserFactory::new()->create();

        $resource = $this->getPreConfiguredResource()
                         ->afterDelete(function(UserModel $model) use ($user) {
                             $this->assertEquals($user->getKey(), $model->getKey());
                             $this->assertFalse($model->exists);
                         });

        $request = $this->deleteRequest($resource, [ $user->id ]);

        (new DeleteController())->handle($request);

    }

    private function getPreConfiguredResource(): AbstractResource
    {
        return $this->makeResource()->addDefaultFields(new EditableField('Name'));
    }

}
