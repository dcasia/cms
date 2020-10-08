<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard;

use DigitalCreative\Dashboard\Http\Requests\BaseRequest;
use DigitalCreative\Dashboard\Resources\AbstractResource;
use Illuminate\Support\Collection;

class Dashboard
{

    private Collection $resources;

    public function setResources(array $resources): self
    {
        $this->resources = collect($resources)
            ->mapWithKeys(fn($resource) => [ $resource::uriKey() => $resource ]);

        return $this;
    }

    public static function getInstance(): Dashboard
    {
        return app(__CLASS__);
    }

    public function allAuthorizedResources(BaseRequest $request): Collection
    {
        /**
         * @todo implement authorized to see
         */
        return $this->resources->map(fn($class, $key) => new $class($request))
                               ->filter(fn(AbstractResource $resource) => $resource)
                               ->values();
    }

    public function resourceForRequest(BaseRequest $request): AbstractResource
    {
        return once(function() use ($request) {

            if ($resource = $this->resources->get($request->route('resource'))) {

                if (!$resource instanceof AbstractResource) {

                    $resource = new $resource();

                }

                return $resource->bootFields($request);

            }

        });
    }

}
