<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Http\Requests;

use DigitalCreative\Dashboard\Dashboard;
use DigitalCreative\Dashboard\Resources\AbstractResource;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    public function resourceInstance(): AbstractResource
    {
        return Dashboard::getInstance()->resourceForRequest($this);
    }

    public function isListing(): bool
    {
        return $this instanceof IndexResourceRequest;
    }

    public function isCreate(): bool
    {
        return $this instanceof StoreResourceRequest
            || $this instanceof FieldsResourceRequest;
    }

    public function isSchemaFetching(): bool
    {
        return $this instanceof FieldsResourceRequest;
    }

    public function isStoringResourceToDatabase(): bool
    {
        return $this instanceof StoreResourceRequest
            || $this instanceof UpdateResourceRequest;
    }

    public function isUpdate(): bool
    {
        return $this instanceof UpdateResourceRequest;
    }

}
