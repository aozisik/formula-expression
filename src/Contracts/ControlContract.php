<?php

namespace Swiftmade\FEL\Contracts;

use Swiftmade\FEL\Parser;
use Symfony\Component\ExpressionLanguage\TokenStream;

interface ControlContract
{
    public function run($directive, TokenStream $stream, Parser $parser);
}