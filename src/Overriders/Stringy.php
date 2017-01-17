<?php

namespace Swiftmade\FEL\Overriders;

use Swiftmade\FEL\Contracts\RecastContract;
use \Swiftmade\FEL\Stringy\Stringy as S;
use Swiftmade\FEL\Contracts\OverriderContract;

class Stringy implements OverriderContract, RecastContract
{
    public function type()
    {
        return 'string';
    }

    public function override($variable)
    {
        return S::create($variable);
    }

    public function resultType()
    {
        return S::class;
    }

    public function recast($result)
    {
        return (string)$result;
    }
}