<?php

namespace Swiftmade\FEL\Overriders;

use Swiftmade\FEL\Contracts\RecastContract;
use Swiftmade\FEL\Contracts\OverriderContract;
use LaravelCollect\Support\Collection as LaravelCollection;

class Collection implements OverriderContract, RecastContract
{
    public function type()
    {
        return 'array';
    }

    public function override($variable)
    {
        return new LaravelCollection($variable);
    }

    public function resultType()
    {
        return LaravelCollection::class;
    }

    public function recast($result)
    {
        return $result->toArray();
    }
}