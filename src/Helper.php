<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Support\Stringy;
use LaravelCollect\Support\Collection;

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

    public function collect(array $array)
    {
        return new Collection($array);
    }
}