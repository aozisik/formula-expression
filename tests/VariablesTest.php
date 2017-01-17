<?php

use Swiftmade\FEL\FormulaExpression;

class VariablesTest extends TestCase
{
    public function testItCanDefineVariables()
    {
        $code = 'a = b + 5;' . PHP_EOL
            . 'a';

        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate($code, [
            'b' => 5
        ]);

        $this->assertEquals(10, $result);
    }
}