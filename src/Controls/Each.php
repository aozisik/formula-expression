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
        $args = array_map('trim', $args);
        
        if(count($args) !== 4) {
            throw new SyntaxError('Invalid foreach syntax: incorrect number of arguments');
        }
    
        // Defaults
        $result = Parser::SKIP;
        $source = $names[$args[1]];
        $keyName = null;
        $varName = $args[3];

        // Re-arrange keyName and varName if => is present in the second argument.
        if(strpos($varName, ' => ') !== false) {
            $varNames = explode(' => ', $varName);
            $keyName = $varNames[0];
            $varName = $varNames[1];
        }

        foreach ($source as $key => $value) {
            $names[$varName] = $value;
            if(!is_null($keyName)) {
                $names[$keyName] = $key;
            }
            $parser = new Parser();
            $result = $parser->parse(new TokenStream($tokens), $names);
            $names = $parser->names;
        }
        return $result;
    }
}

