<?php

namespace Swiftmade\FEL\Controls;

use Swiftmade\FEL\Parser;
use Swiftmade\FEL\TokenStream;
use Swiftmade\FEL\Contracts\ControlContract;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Condition implements ControlContract
{
    public function run($directive, array $tokens, array &$names)
    {
        $expressionLanguage = new ExpressionLanguage();
        if (!$expressionLanguage->evaluate($directive, $names)) {
            return Parser::SKIP;
        }

        $parser = new Parser();
        $result = $parser->parse(new TokenStream($tokens), $names);
        $names = $parser->names;
        return $result;
    }
}

