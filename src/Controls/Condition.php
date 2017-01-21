<?php

namespace Swiftmade\FEL\Controls;

use Swiftmade\FEL\Parser;
use Swiftmade\FEL\Contracts\ControlContract;
use Symfony\Component\ExpressionLanguage\TokenStream;

class Condition implements ControlContract
{
    public function run($directive, TokenStream $tokenStream, Parser $parser)
    {
        return Parser::SKIP;
    }
}

