<?php

namespace Swiftmade\FEL\Contracts;

interface ControlContract
{
    public function run($directive, array $tokens, array &$names);
}