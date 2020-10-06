<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Repository;

use DigitalCreative\Dashboard\FieldsData;
use DigitalCreative\Dashboard\FilterCollection;
use DigitalCreative\Dashboard\Http\Requests\BaseRequest;
use DigitalCreative\Dashboard\Resources\EloquentResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Repository implements RepositoryInterface
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
        return tap($this->newQuery(), static fn(Builder $builder) => $userDefinedCallback($builder, $request))->get();
    }

    public function getOptionsForRelatedResource(callable $userDefinedCallback, BaseRequest $request): Collection
    {
        return $this->searchForRelatedEntries($userDefinedCallback, $request);
    }

    /**
     * Whatever is returned from this method is sent back to the client after the creation
     *
     * @param FieldsData $data
     *
     * @return mixed
     */
    public function create(FieldsData $data)
    {
        return $this->newModel()->forceFill($data->toArray())->save();
    }

    public function batchDelete(array $ids): bool
    {
        return (bool) $this->newQuery()->whereIn('id', $ids)->delete();
    }

    public function count(FilterCollection $filters): int
    {
        return $this->applyFilterToQuery($filters)->count();
    }

    public function findCollection(FilterCollection $filters, int $page, int $perPage = 15): Collection
    {
        return $this->applyFilterToQuery($filters)->forPage($page, $perPage)->get();
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

    private function applyFilterToQuery(FilterCollection $filters): Builder
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
