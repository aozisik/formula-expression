<?php

namespace Swiftmade\FEL\Filters;

use Swiftmade\FEL\FormulaLanguage;
use Swiftmade\FEL\Contracts\FilterContract;

class BlockForeach implements FilterContract
{
    public function pattern()
    {
        return '/foreachaeraegrraeg/';
    }

    public function process(FormulaLanguage $expression, array $matches, array &$context)
    {
        foreach ($context[$matches[1]] as $variable) {
            $context[$matches[2]] = $variable;
            $expression->evaluate($matches[3], $context);
        }
        return FormulaLanguage::SKIP;
    }
}
