<?php

namespace Swiftmade\FEL\Contracts;

use Swiftmade\FEL\FormulaExpression;

interface FilterContract
{
    public function pattern();

    public function process(FormulaExpression $expression, array $matches, array &$context);
}