<?php

namespace Swiftmade\FEL\Contracts;

interface RecastContract
{
    public function resultType();

    public function recast($result);
}