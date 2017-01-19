<?php

namespace Swiftmade\FEL\Contracts;

use Swiftmade\FEL\FormulaLanguage;

interface FilterContract
{
    public function pattern();

    public function process(FormulaLanguage $expression, array $matches, array &$context);
}