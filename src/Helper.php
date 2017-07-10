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

    public function replace(array $dictionary, $subject)
    {
        if (is_array($subject)) {
            return array_map(function ($item) use ($dictionary) {
                return $this->replace($dictionary, $item);
            }, $subject);
        }
        $keys = array_keys($dictionary);
        $values = array_values($dictionary);
        $uniqIds = [];
        for($i = 0; $i<count($values); $i++) {
            $uniqIds[] = uniqid();
        }
        $output = str_replace($keys, $uniqIds, $subject);
        return str_replace($uniqIds, $values, $output);
    }

    public function select(array $source, array $keys)
    {
        return array_map(function ($item) use ($keys) {
            $values = [];
            foreach ($keys as $key) {
                $values[] = array_get($item, $key);
            }
            return $values;
        }, $source);
    }

    public function enumerate($item)
    {
        if (is_array($item) and isset($item[0])) {
            return $item;
        }
        return [$item];
    }
}