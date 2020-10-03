<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Traits;

use DigitalCreative\Dashboard\Fields\AbstractField;
use DigitalCreative\Dashboard\Fields\BelongsToField;
use DigitalCreative\Dashboard\FilterCollection;
use DigitalCreative\Dashboard\Http\Requests\IndexResourceRequest;
use DigitalCreative\Dashboard\Repository\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait OperationTrait
{
    public function detail(): array
    {
        $model = $this->findResource();

        return [
            'key' => $model->getKey(),
            'fields' => $this->resolveFieldsUsingModel($model)->jsonSerialize(),
        ];
    }

//    public function store(): void
//    {
//
//        $bag = new FieldsData();
//
//        $request = $this->getRequest();
//
//        $fields = $this->resolveFields($request);
//
//        $this->validateFields($fields, $request);
//
//        $callbacks = $this->filterNonUpdatableFields($fields)
//                          ->map(fn(AbstractField $field) => $field->fillUsingRequest($bag, $request));
//
//        $this->repository()->create($bag);
//
//        $callbacks->filter()->each(fn(callable $function) => $function());
//
//    }

    public function delete(): bool
    {
        return $this->repository()->deleteResource($this->findResource());
    }

    public function index(IndexResourceRequest $request): array
    {

        $fields = $this->resolveFields($request);

        $filters = new FilterCollection($this->resolveFilters(), $request->query('filters'));

        $total = $this->repository()->count($filters);

        $resources = $this->repository()
                          ->findCollection($filters, (int) $this->request->query('page', 1))
                          ->map(static function(Model $model) use ($request, $fields) {

                              return [
                                  'key' => $model->getKey(),
                                  'fields' => $fields->map(fn(AbstractField $field) => (clone $field)->resolveUsingModel($request, $model)),
                              ];

                          });

        return [
            'total' => $total,
            'resources' => $resources,
        ];

    }

    public function searchBelongsToRelation(): Collection
    {

        $request = $this->getRequest();

        $field = $this->findFieldByAttribute($request->route('field'));

        if ($field instanceof BelongsToField && $field->isSearchable()) {

            $resource = $field->getRelatedResource();
            $repository = new Repository($resource->getModel());

            $models = $repository->searchForRelatedEntries(
                $field->resolveSearchCallback(), $request
            );

            return $models->map(static function(Model $model) use ($resource) {
                return collect($resource->resolveFieldsUsingModel($model)->jsonSerialize())->pluck('value', 'attribute');
            });

        }

        return abort(404);

    }

    private function findResource(): ?Model
    {
        return once(function() {
            return $this->repository()->findByKey(
                $this->request->route('key')
            );
        });
    }

}
