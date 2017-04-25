<?php

namespace Swiftmade\FEL\Controls;

use Swiftmade\FEL\Parser;
use Swiftmade\FEL\TokenStream;
use Swiftmade\FEL\Contracts\ControlContract;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class Each implements ControlContract
{
    public function run($directive, array $tokens, array &$names)
    {
        preg_match('/(.*)(\sas\s)(.*)/', $directive, $args);

        if(count($args) !== 4) {
            throw new SyntaxError('Invalid foreach syntax: incorrect number of arguments');
        }

        $args = array_map('trim', $args);

        $result = Parser::SKIP;
        $variable = $args[3];
        $source = $names[$args[1]];

        foreach ($source as $value) {
            $names[$variable] = $value;
            $parser = new Parser();
            $result = $parser->parse(new TokenStream($tokens), $names);
            $names = $parser->names;
        }

        return $result;
    }
}

