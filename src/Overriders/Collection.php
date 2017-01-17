<?php

namespace Swiftmade\FEL\Overriders;

use DusanKasan\Knapsack\Collection as KnapsackCollection;
use Swiftmade\FEL\Contracts\RecastContract;
use Swiftmade\FEL\Contracts\OverriderContract;

class Collection implements OverriderContract, RecastContract
{
    public function type()
    {
        return 'array';
    }

    public function override($variable)
    {
        return new KnapsackCollection($variable);
    }

    public function resultType()
    {
        return KnapsackCollection::class;
    }

    public function recast($result)
    {
        return $result->toArray();
    }
}