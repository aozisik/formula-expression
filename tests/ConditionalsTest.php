<?php

use Swiftmade\FEL\FormulaExpression;

class ConditionalsTest extends TestCase
{
    public function testItHandlesInlineConditionals()
    {
        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate('apples if(oranges == 51)', [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(30, $result);
    }

    public function testFirstSatisfactoryConditionalWins()
    {
        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate('apples if(oranges > 50);' . PHP_EOL .
            'oranges if(apples > 20)', [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(30, $result);
    }

    public function testItHandlesBlockConditionals()
    {
        $code = "if(oranges > 50) {" . PHP_EOL
            . "\tapples" . PHP_EOL
            . "}";

        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate($code, [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(30, $result);
    }
}