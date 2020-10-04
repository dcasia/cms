<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Concerns;

use DigitalCreative\Dashboard\FieldsData;
use DigitalCreative\Dashboard\Http\Requests\StoreResourceRequest;

interface WithCustomStore
{
    /**
     * The return of this function is sent back to the client after the creation
     * Avoid returning sensitive information, like raw user passwords
     *
     * @param FieldsData $data
     * @param StoreResourceRequest $request
     * @return mixed
     */
    public function storeResource(FieldsData $data, StoreResourceRequest $request);
}
