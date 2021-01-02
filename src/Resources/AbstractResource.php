<?php

declare(strict_types = 1);

namespace DigitalCreative\Jaqen\Resources;

use DigitalCreative\Jaqen\Concerns\WithEvents;
use DigitalCreative\Jaqen\Http\Requests\BaseRequest;
use DigitalCreative\Jaqen\Repository\Repository;
use DigitalCreative\Jaqen\Repository\RepositoryInterface;
use DigitalCreative\Jaqen\Traits\EventsTrait;
use DigitalCreative\Jaqen\Traits\MakeableTrait;
use DigitalCreative\Jaqen\Traits\ResolveFieldsTrait;
use DigitalCreative\Jaqen\Traits\ResolveFiltersTrait;
use DigitalCreative\Jaqen\Traits\ResolveUriKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AbstractResource implements WithEvents
{

    use ResolveFieldsTrait;
    use ResolveFiltersTrait;
    use ResolveUriKey;
    use MakeableTrait;
    use EventsTrait;

    private BaseRequest $request;
    private RepositoryInterface $repository;

    public array $with = [];

    abstract public function model(): Model;

    public function with(array $with, bool $override = true): self
    {

        if ($override) {

            $this->with = array_merge($this->with, $with);

        } else {

            $currentWith = collect($this->with);

            foreach ($with as $relation => $item) {

                $isNumericKey = is_numeric($relation);
                $relationKey = $isNumericKey ? $item : $relation;

                if ($currentWith->contains($relationKey) || $currentWith->has($relationKey)) {
                    continue;
                }

                if ($isNumericKey) {

                    $this->with[] = $relationKey;

                } else {

                    $this->with[ $relationKey ] = $item;

                }

            }

        }

        return $this;
    }

    public function bootFields(BaseRequest $request): self
    {
        $this->resolveFields($request);

        return $this;
    }

    public function perPage(BaseRequest $request): int
    {
        return $this->model()->getPerPage();
    }

    public function getDescriptor(): array
    {
        return [
            'name' => $this->label(),
            'label' => Str::plural($this->label()),
            'uriKey' => static::uriKey(),
        ];
    }

    public static function humanize(string $value): string
    {
        return Str::title(Str::snake($value, ' '));
    }

    public function label(): string
    {
        return static::humanize(class_basename(static::class));
    }

    public function useRepository(RepositoryInterface $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function repository(): RepositoryInterface
    {
        return $this->repository ?? new Repository($this->model());
    }

}
