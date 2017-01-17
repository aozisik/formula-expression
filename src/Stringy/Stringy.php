<?php

namespace Swiftmade\FEL\Stringy;

use Stringy\Stringy as S;

class Stringy extends S
{
    public function toNumber()
    {
        return floatval($this->str);
    }
}
