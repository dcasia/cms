<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Tests\Controllers;

use DigitalCreative\Dashboard\Tests\Factories\UserFactory;
use DigitalCreative\Dashboard\Tests\TestCase;

class ResourceControllerTest extends TestCase
{

    public function test_resource_list_api(): void
    {

        UserFactory::new()->create();

        $response = $this->getJson('/dashboard-api/resources')
                         ->assertStatus(200);

        $response->assertJsonStructure([
            [
                'name',
                'label',
                'uriKey',
            ],
        ]);

    }

}
