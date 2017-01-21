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

    public function testItHandlesNestedConditionals()
    {
        $code = 'if(a == 5) {' . PHP_EOL
            . 'if(b == 3) {' . PHP_EOL
            . 'if(c == 4) {' . PHP_EOL
            . 'd = 5;' . PHP_EOL
            . '}}}d;';

        $evaluator = new FormulaLanguage();
        $this->assertEquals(5, $evaluator->evaluate(
            $code, [
            'a' => 5,
            'b' => 3,
            'c' => 4
        ]));
    }

    public function testItHandlesElseIf()
    {
        $code = 'if(a == 6) {' . PHP_EOL
            . 'b = 2;' . PHP_EOL
            . '} elseif(a == 6) {' . PHP_EOL
            . 'b = 3;'
            . '} elseif(a == 7) {' . PHP_EOL
            . 'b = 5;' . PHP_EOL
            . '}b;';

        $evaluator = new FormulaLanguage();
        $this->assertEquals(2, $evaluator->evaluate(
            $code, [
            'a' => 6
        ]));

        $evaluator = new FormulaLanguage();
        $this->assertEquals(5, $evaluator->evaluate(
            $code, [
            'a' => 7
        ]));
    }
}