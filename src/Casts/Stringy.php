<?php

namespace Swiftmade\FEL\Casts;

use Swiftmade\FEL\Contracts\CastContract;

class Stringy implements CastContract
{
    public function type()
    {
        return \Swiftmade\FEL\Support\Stringy::class;
    }

    public function cast($value)
    {
        return (string)$value;
    }

}