<?php

use Swiftmade\FEL\FormulaLanguage;

class BasicTest extends TestCase
{
    public function testItReturnsCorrectTypes()
    {
        $evaluator = new FormulaLanguage();

        // String (single-quotes)
        $output = $evaluator->evaluate("'5'");
        $this->assertTrue(is_string($output));
        // String (double-quotes)
        $output = $evaluator->evaluate('"5"');
        $this->assertEquals('5', $output);
        $this->assertTrue(is_string($output));

        // Integer
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('5');
        $this->assertTrue(is_int($output));

        // Boolean
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('true');
        $this->assertTrue(is_bool($output));

        // Null
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('null');
        $this->assertTrue(is_null($output));
    }

    public function testItReturnsArrays()
    {
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('[1, a, 3]', [
            'a' => 2
        ]);
        $this->assertEquals([1, 2, 3], $output);
    }

    public function testItCalculates()
    {
        $evaluator = new FormulaLanguage();
        $this->assertEquals(21, $evaluator->evaluate('5 * 4 + 1'));
        // Calculate using variables
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('a + b + 3', [
            'a' => 5,
            'b' => 4
        ]);
        $this->assertEquals(12, $output);
        // Convert strings to numbers for mathematical operations
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('a + b', [
            'a' => '5',
            'b' => 4
        ]);
        $this->assertEquals(9, $output);
    }

    public function testItSkipsReturn()
    {
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('#5;6');
        $this->assertEquals(6, $output);
    }
}