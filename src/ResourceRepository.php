<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard;

use DigitalCreative\Dashboard\Http\Requests\BaseRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ResourceRepository
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * ResourceRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function searchForRelatedEntries(callable $userDefinedCallback, BaseRequest $request): Collection
    {
        return tap($this->newQuery(), fn(Builder $builder) => $userDefinedCallback($builder, $request))->get();
    }

    public function create(FieldsData $data): bool
    {
        return $this->newModel()->forceFill($data->toArray())->save();
    }

    public function count(FilterCollection $filters): int
    {
        return $this->applyFilterToQuery($filters)->count();
    }

    public function findCollection(FilterCollection $filters, int $page): Collection
    {
        return $this->applyFilterToQuery($filters)->forPage($page)->get();
    }

    public function findByKey(string $key): ?Model
    {
        return $this->newQuery()->whereKey($key)->firstOrFail();
    }

    public function updateResource(Model $model, array $data): bool
    {
        $model->forceFill($data);

        if ($model->isDirty()) {

            return $model->save();

        }

        return true;
    }

    private function applyFilterToQuery(FilterCollection $filters)
    {
        return $filters->applyOnQuery($this->newQuery());
    }

    private function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    private function newModel(): Model
    {
        return $this->model->newInstance();
    }

}
