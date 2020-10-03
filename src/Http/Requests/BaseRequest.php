<?php

declare(strict_types = 1);

namespace DigitalCreative\Dashboard\Http\Requests;

use DigitalCreative\Dashboard\Resources\Resource;
use DigitalCreative\Dashboard\Dashboard;
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

    public function resourceInstance(): Resource
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
            || $this instanceof CreateResourceRequest;
    }

    public function isUpdate(): bool
    {
        return $this instanceof UpdateResourceRequest;
    }

}
