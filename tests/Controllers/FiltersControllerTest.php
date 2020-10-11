<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Tests\Controllers;

use DigitalCreative\Dashboard\Tests\TestCase;

class FiltersControllerTest extends TestCase
{

    public function test_resource_filters_works(): void
    {

        $this->getJson('/dashboard-api/users/filters')
             ->assertStatus(200)
             ->assertJsonStructure([
                 [
                     'uriKey',
                     'fields' => [
                         [
                             'label',
                             'attribute',
                             'value',
                             'component',
                             'additionalInformation' => [
                                 'male',
                                 'female',
                             ],
                         ],
                     ],
                 ],
             ]);

    }

}
