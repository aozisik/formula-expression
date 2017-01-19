<?php

use Swiftmade\FEL\FormulaExpression;

class ForeachTest extends TestCase
{
    public function testItHandlesForeachLoops()
    {
        $code = 'fullname = "";' . PHP_EOL
            . "foreach(names as name) {" . PHP_EOL
            . "fullname = fullname ~ ' ' ~ name" . PHP_EOL
            . "};";

        $evaluator = new FormulaExpression();
        $result = $evaluator->evaluate($code, [
            'names' => ['Ahmet', 'Özışık']
        ]);
        $this->assertEquals('Ahmet Özışık', $result);
    }
}