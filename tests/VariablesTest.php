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

    public function testVariableDeclarationSurvivesIfBlock()
    {
        $code = 'a = 5;' . PHP_EOL
            . 'if(a == 5) {' . PHP_EOL
            . 'b = 3;' . PHP_EOL
            . '}' . PHP_EOL
            . 'b';

        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate($code);
        $this->assertEquals(3, $result);
    }
}