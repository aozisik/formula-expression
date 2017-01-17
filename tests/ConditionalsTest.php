<?php

use Swiftmade\FEL\FormulaExpression;

class ConditionalsTest extends TestCase
{
    public function testItHandlesInlineConditionals()
    {
        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate('apples if(oranges > 50)', [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(30, $result);
    }
}