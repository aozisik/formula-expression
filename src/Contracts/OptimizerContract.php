<?php

namespace Swiftmade\FEL\Contracts;

use Swiftmade\FEL\TokenStream;

interface OptimizerContract
{
    public function optimize(TokenStream $stream, array &$tokens);
}