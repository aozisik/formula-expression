<?php

namespace Swiftmade\FEL\Filters;

use Swiftmade\FEL\FormulaExpression;
use Swiftmade\FEL\Contracts\FilterContract;

class InlineIf implements FilterContract
{
    public function pattern()
    {
        return '/(.*) if\((.*)\)/';
    }

    public function process(FormulaExpression $expression, array $matches, array $context)
    {
        if (!$expression->evaluate($matches[2], $context)) {
            return FormulaExpression::SKIP;
        }

        return $expression->evaluate($matches[1], $context);
    }
}