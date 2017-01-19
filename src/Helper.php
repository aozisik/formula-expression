<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Support\Stringy;
use LaravelCollect\Support\Collection;

class Helper
{
    protected $context;

    public function __construct(&$context)
    {
        $this->context = $context;
    }

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

    public function set($variable, $value)
    {
        $this->context[$variable] = $value;
        return FormulaLanguage::SKIP;
    }

    public function replace(array $dictionary, $subject)
    {
        if (is_array($subject)) {
            return array_map(function ($item) use ($dictionary) {
                return $this->replace($dictionary, $item);
            }, $subject);
        }
        return str_replace(array_keys($dictionary), array_values($dictionary), $subject);
    }

    public function select(array $source, array $keys)
    {
        return array_map(function ($item) use ($keys) {
            return array_only($item, $keys);
        }, $source);
    }
}