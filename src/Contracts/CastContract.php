<?php

namespace Swiftmade\FEL\Contracts;

interface CastContract
{
    public function type();

    public function cast($value);
}