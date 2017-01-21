<?php

namespace Swiftmade\FEL\Controls;

use Swiftmade\FEL\Parser;
use Swiftmade\FEL\Contracts\ControlContract;
use Symfony\Component\ExpressionLanguage\TokenStream;

class Each implements ControlContract
{
    public function run($directive, array $tokens, array &$names)
    {
        $args = explode('as', $directive);
        $args = array_map('trim', $args);

        $result = Parser::SKIP;
        $variable = $args[1];
        $source = $names[$args[0]];

        foreach ($source as $value) {
            $names[$variable] = $value;
            $parser = new Parser();
            $result = $parser->parse(new TokenStream($tokens), $names);
            $names = $parser->names;
        }

        return $result;
    }
}

