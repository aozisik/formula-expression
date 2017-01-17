<?php

namespace Swiftmade\FEL\Filters;

use Swiftmade\FEL\FormulaExpression;
use Swiftmade\FEL\Contracts\FilterContract;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class BlockIf implements FilterContract
{
    public function pattern()
    {
        return '/if\((.*)\) \{(.*)\}/';
    }

    public function process(FormulaExpression $expression, array $matches, array &$context)
    {
        $language = new ExpressionLanguage();
        if (!$language->evaluate($matches[1], $context)) {
            return FormulaExpression::SKIP;
        }
        return $expression->evaluate($matches[2], $context);
    }
}
