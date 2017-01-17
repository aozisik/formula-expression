<?php

use Swiftmade\FEL\FormulaExpression;

class OverrideTest extends TestCase
{
    public function testItCastsStringVariables()
    {
        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate('a.split(",")', [
            'a' => 'apple,banana,pineapple'
        ]);
        $this->assertEquals(['apple', 'banana', 'pineapple'], $result);
    }

    public function testItCastsStringsBack()
    {
        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate('a.trim()', [
            'a' => ' a '
        ]);
        $this->assertTrue(is_string($result));
        $this->assertEquals('a', $result);
    }
}