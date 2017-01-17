<?php

namespace Swiftmade\FEL\Contracts;

interface OverriderContract
{
    public function type();

    public function override($variable);
}