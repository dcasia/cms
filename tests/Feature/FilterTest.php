<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Tests\Feature;

use DigitalCreative\Dashboard\AbstractResource;
use DigitalCreative\Dashboard\Fields\EditableField;
use DigitalCreative\Dashboard\FieldsData;
use DigitalCreative\Dashboard\FilterCollection;
use DigitalCreative\Dashboard\Http\Requests\BaseRequest;
use DigitalCreative\Dashboard\Tests\Fixtures\Filters\SampleFilter;
use DigitalCreative\Dashboard\Tests\Fixtures\Models\User as UserModel;
use DigitalCreative\Dashboard\Tests\TestCase;
use DigitalCreative\Dashboard\Tests\Traits\RequestTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;


class FilterTest extends TestCase
{

    use RequestTrait;

    public function test_filter_works(): void
    {

        /**
         * @var UserModel $user
         */
        $user = factory(UserModel::class)->create([ 'name' => 'Demo' ]);

        factory(UserModel::class, 10)->create();

        $filter = new class extends SampleFilter {

            public function apply(Builder $builder, FieldsData $value): Builder
            {
                return $builder->where('name', 'Demo');
            }

        };

        $resource = $this->getResource(
            BaseRequest::create('/', 'GET', [ 'filters' => FilterCollection::test([ $filter::uriKey() => null ]) ])
        );

        $resource->addFilters($filter);

        $result = $resource->index();

        $this->assertSame($result['total'], 1);
        $this->assertEquals($user->id, data_get($result, 'resources.0.key'));
        $this->assertEquals($user->name, data_get($result, 'resources.0.fields.0.value'));

    }

    public function test_filter_validation_works(): void
    {

        $filter = new class extends SampleFilter {

            public function apply(Builder $builder, FieldsData $value): Builder
            {
                return $builder;
            }

            public function fields(): array
            {
                return [
                    (new EditableField('Name'))->rules('required', 'min:3'),
                ];
            }

        };

        $resource = $this->getResource(
            BaseRequest::create('/', 'GET', [ 'filters' => FilterCollection::test([ $filter::uriKey() => null ]) ])
        );

        $resource->addFilters($filter);

        $this->expectException(ValidationException::class);

        $resource->index();

    }

    public function test_multiple_filter_validation_works(): void
    {

        $filter1 = new class extends SampleFilter {

            public function fields(): array
            {
                return [
                    (new EditableField('Name'))->rules('required'),
                ];
            }

        };

        $filter2 = new class extends SampleFilter {

            public function fields(): array
            {
                return [
                    (new EditableField('Gender'))->rules('required'),
                ];
            }

        };

        $filters = FilterCollection::test([
            $filter1::uriKey() => [ 'name' => 'Demo' ],
            $filter2::uriKey() => [ 'gender' => null ],
        ]);

        $request = $this->makeRequest('/', 'GET', [ 'filters' => $filters ]);

        $resource = $this->getResource($request);

        $resource->addFilters($filter1);
        $resource->addFilters($filter2);

        $this->expectException(ValidationException::class);

        $resource->index();

    }

    public function test_value_from_the_fields_are_passed_correctly_to_the_apply_method(): void
    {

        $filter = new class($this) extends SampleFilter {

            private FilterTest $runner;

            public function __construct(FilterTest $runner)
            {
                $this->runner = $runner;
            }

            public function apply(Builder $builder, FieldsData $value): Builder
            {
                $this->runner->assertSame([ 'hello', 'world' ], $value->get('array'));
                $this->runner->assertSame('hello world', $value->get('string'));
                $this->runner->assertSame(2020, $value->get('int'));
                $this->runner->assertSame([ 'hello' => 'world' ], $value->get('object'));

                return $builder;
            }

            public function fields(): array
            {
                return [
                    new EditableField('Array'),
                    new EditableField('String'),
                    new EditableField('Int'),
                    new EditableField('Object'),
                ];
            }

        };

        $filters = FilterCollection::test([
            $filter::uriKey() => [
                'array' => [ 'hello', 'world' ],
                'string' => 'hello world',
                'int' => 2020,
                'object' => [ 'hello' => 'world' ],
            ],
        ]);

        $request = $this->makeRequest('/', 'GET', [ 'filters' => $filters ]);

        $this->getResource($request)
             ->addFilters($filter)
             ->index();

    }

    private function getResource(BaseRequest $request): AbstractResource
    {

        return new class($request) extends AbstractResource {

            public static string $model = UserModel::class;

            public function fields(): array
            {
                return [
                    new EditableField('name'),
                ];
            }

        };

    }

}
