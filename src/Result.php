<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Contracts\CastContract;

class Result
{
    protected $result;
    protected $casts = [];

    public function __construct($result)
    {
        $this->result = $result;
        $this->registerTypeCast(new Casts\Stringy);
        $this->registerTypeCast(new Casts\Collection);
    }

    protected function registerTypeCast(CastContract $cast)
    {
        $this->casts[$cast->type()] = $cast;
    }

    protected function resultType()
    {
        $type = gettype($this->result);
        if ($type === 'object') {
            return get_class($this->result);
        }
        return $type;
    }

    public function output()
    {
        $type = $this->resultType();
        if (isset($this->casts[$type])) {
            return $this->casts[$type]->cast($this->result);
        }
        return $this->result;
    }
}