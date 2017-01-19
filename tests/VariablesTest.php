<?php

use Swiftmade\FEL\FormulaLanguage;

class VariablesTest extends TestCase
{
    public function testItCanDefineVariables()
    {
        $code = 'a = b + 5;' . PHP_EOL
            . 'a';

        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate($code, [
            'b' => 5
        ]);

        $this->assertEquals(10, $result);
    }
}