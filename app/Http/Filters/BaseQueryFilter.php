<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BaseQueryFilter
{
    protected Builder $builder;

    protected $filters = [];

    public function __construct(private readonly Request $request)
    {
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    public function getFilters(): array
    {
        return rescue(
            callback: fn() => json_decode(base64_decode($this->request->filters), true),
            rescue: null,
            report: null
        ) ?: [];
    }
}
