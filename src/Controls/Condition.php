<?php

namespace Swiftmade\FEL\Controls;

use Swiftmade\FEL\Token;
use Swiftmade\FEL\Parser;
use Swiftmade\FEL\Contracts\ControlContract;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\TokenStream;

class Condition implements ControlContract
{
    public function run($directive, array $tokens, array &$names)
    {
        $expressionLanguage = new ExpressionLanguage();
        if (!$expressionLanguage->evaluate($directive, $names)) {
            return Parser::SKIP;
        }

        $parser = new Parser();
        return $parser->parse(new TokenStream($tokens), $names);
    }
}

