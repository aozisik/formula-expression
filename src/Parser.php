<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\TokenStream;

class Parser
{
    public function parse(TokenStream $tokenStream, array $names)
    {
        $returnableValue = FormulaLanguage::SKIP;

        while ($returnableValue === FormulaLanguage::SKIP) {
            $token = $tokenStream->current;
            echo $token->type;

            if ($tokenStream->isEOF()) {
                break;
            }
            $tokenStream->next();
        }
    }
}