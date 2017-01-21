<?php

use Swiftmade\FEL\FormulaLanguage;

class ConditionalsTest extends TestCase
{
    public function testItHandlesInlineConditionals()
    {
        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate('apples if(oranges == 51)', [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(30, $result);
    }

    public function testFirstSatisfactoryConditionalWins()
    {
        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate('apples if(oranges > 52);' . PHP_EOL .
            'oranges if(apples > 20)', [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(51, $result);
    }

    public function testItHandlesBlockConditionals()
    {
        $code = "if(oranges > 50) {" . PHP_EOL
            . "\tapples" . PHP_EOL
            . "}";

        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate($code, [
            'apples' => 30,
            'oranges' => 51
        ]);

        $this->assertEquals(30, $result);
    }

    public function testItAllowsParenthesesInsideConditional()
    {
        $code = 'test if((5+5) + (3*(2+1)) == 19)';
        $evaluator = new FormulaLanguage();
        $this->assertEquals('yep', $evaluator->evaluate(
            $code, [
            'test' => 'yep'
        ]));

    }
}