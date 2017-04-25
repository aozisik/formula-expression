<?php

use Swiftmade\FEL\FormulaLanguage;

class ForeachTest extends TestCase
{
    public function testItHandlesForeachLoops()
    {
        $code = 'fullname = "";' . PHP_EOL
            . "foreach(names as name) {" . PHP_EOL
            . 'fullname = fullname ~ " " ~ name;' . PHP_EOL
            . "}" . PHP_EOL
            . '_.str(fullname).trim();';

        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate($code, [
            'names' => ['Ahmet', 'Özışık']
        ]);
        $this->assertEquals('Ahmet Özışık', $result);
    }

    public function testForeachArgumentsCanContainAs()
    {
        $code = 'foreach(measures as measure) {' . PHP_EOL
            . '}';
        $evaluator = new FormulaLanguage();
        $evaluator->evaluate($code, [
            'measures' => []
        ]);
        $this->addToAssertionCount(1);
    }

    public function testForeachProvidesAccessToCurrentKey()
    {
        $code = 'keys = "";' . PHP_EOL
            . "foreach(vars as key => name) {" . PHP_EOL
            . 'keys = keys ~ key;' . PHP_EOL
            . "}" . PHP_EOL
            . 'keys;';

        $evaluator = new FormulaLanguage();
        $result = $evaluator->evaluate($code, [
            'vars' => ['a' => 'bar', 'b' => 'foo']
        ]);
        $this->assertEquals('ab', $result);
    }
}      