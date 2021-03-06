<?php

namespace Swiftmade\FEL\Support;

use Stringy\Stringy as S;

class Stringy extends S
{
    public function toNumber()
    {
        return floatval($this->str);
    }

    public function split($delimiter, $limit = null) {
    	return explode($delimiter, $this->str);
    }
}
