<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Tests\Controllers\Resources;

use DigitalCreative\Jaqen\Tests\Factories\UserFactory;
use DigitalCreative\Jaqen\Tests\TestCase;

class DeleteControllerTest extends TestCase
{

    public function test_resource_delete(): void
    {
        $data = [
            'name' => 'Demo',
            'email' => 'email@email.com',
        ];

        UserFactory::new()->create($data);

        $this->deleteJson('/jaqen-api/users', [ 'ids' => [ 1 ] ])
             ->assertStatus(204);

        $this->assertDatabaseMissing('users', $data);
    }

    public function test_deleting_multiple_items_works(): void
    {
        $users = UserFactory::new()->count(3)->create();

        UserFactory::new()->create();

        $this->deleteJson('/jaqen-api/users', [ 'ids' => $users->pluck('id') ])
             ->assertStatus(204);

        $this->assertDatabaseCount('users', 1);
    }

}
