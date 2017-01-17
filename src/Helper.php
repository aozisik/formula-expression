<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Stringy\Stringy;

class Helper
{
    /**
     * @param $str
     * Converts a string to Stringy object
     * @return \Stringy\Stringy
     */
    public function str($str)
    {
        return Stringy::create($str);
    }
}