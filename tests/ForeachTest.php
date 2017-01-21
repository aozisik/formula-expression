<?php

use Swiftmade\FEL\FormulaLanguage;

class ForeachTest extends TestCase
{
    public function testItHandlesForeachLoops()
    {
        $code = 'fullname = "";' . PHP_EOL
            . "foreach(names as name) {" . PHP_EOL
            . 'fullname = fullname ~ " " ~ name;' . PHP_EOL
            . "}";

        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate($code, [
            'names' => ['Ahmet', 'Özışık']
        ]);
        $this->assertEquals('Ahmet Özışık', $result);
    }
}