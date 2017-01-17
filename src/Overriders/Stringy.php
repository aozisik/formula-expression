<?php

namespace Swiftmade\FEL\Overriders;

use \Swiftmade\FEL\Stringy\Stringy as S;
use Swiftmade\FEL\Contracts\OverriderContract;

class Stringy implements OverriderContract
{
    public function type()
    {
        return 'string';
    }

    public function override($variable)
    {
        return S::create($variable);
    }
}