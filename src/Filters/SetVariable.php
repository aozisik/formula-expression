<?php

namespace Swiftmade\FEL\Filters;

use Swiftmade\FEL\FormulaLanguage;
use Swiftmade\FEL\Contracts\FilterContract;

class SetVariable implements FilterContract
{
    public function pattern()
    {
        return '/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s?=(.*)/';
    }

    public function process(FormulaLanguage $expression, array $matches, array &$context)
    {
        $context[trim($matches[1])] = $expression->evaluate(trim($matches[2]), $context);
        return FormulaLanguage::SKIP;
    }
}