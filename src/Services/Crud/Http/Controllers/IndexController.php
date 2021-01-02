<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Services\Crud\Http\Controllers;

use DigitalCreative\Jaqen\FilterCollection;
use DigitalCreative\Jaqen\Services\Crud\Http\Requests\IndexResourceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

class IndexController extends Controller
{

    private int $perPage;
    private int $currentPage;

    public function handle(IndexResourceRequest $request): JsonResponse
    {

        $resource = $request->resourceInstance();

        $fields = $resource->resolveFields($request);

        $this->currentPage = (int) $request->query('page', 1);
        $this->perPage = $resource->perPage($request);

        $filters = new FilterCollection($resource->resolveFilters(), $request->query('filters'));

        $total = $resource->repository()->count($filters);

        $resources = $resource->repository()
                              ->find($filters, $this->currentPage, $this->perPage, $resource->with)
                              ->map(function (Model $model) use ($request, $fields) {

                                  return [
                                      'key' => $model->getKey(),
                                      'fields' => $fields->getResolvedFieldsData($model, $request),
                                  ];

                              });

        return response()->json([
            'total' => $total,
            'from' => $this->firstItem($resources),
            'to' => $this->lastItem($resources),
            'currentPage' => $this->currentPage,
            'lastPage' => max((int) ceil($total / $this->perPage), 1),
            'resources' => $resources,
        ]);
    }

    public function firstItem(Collection $resources): ?int
    {
        return $resources->isNotEmpty() ? ($this->currentPage - 1) * $this->perPage + 1 : null;
    }

    public function lastItem(Collection $resources): ?int
    {
        return $resources->isNotEmpty() ? $this->firstItem($resources) + $resources->count() - 1 : null;
    }

}
