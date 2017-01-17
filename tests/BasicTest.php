<?php

use Swiftmade\FEL\FormulaExpression;

class BasicTest extends TestCase
{
    public function testItReturnsCorrectTypes()
    {
        $evaluator = new FormulaExpression();

        // String (single-quotes)
        $output = $evaluator->evaluate("'5'");
        $this->assertTrue(is_string($output));
        // String (double-quotes)
        $output = $evaluator->evaluate('"5"');
        $this->assertEquals('5', $output);
        $this->assertTrue(is_string($output));

        // Integer
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('5');
        $this->assertTrue(is_int($output));

        // Boolean
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('true');
        $this->assertTrue(is_bool($output));

        // Null
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('null');
        $this->assertTrue(is_null($output));
    }

    public function testItReturnsArrays()
    {
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('[1, a, 3]', [
            'a' => 2
        ]);
        $this->assertEquals([1, 2, 3], $output);
    }

    public function testItCalculates()
    {
        $evaluator = new FormulaExpression();
        $this->assertEquals(21, $evaluator->evaluate('5 * 4 + 1'));
        // Calculate using variables
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('a + b + 3', [
            'a' => 5,
            'b' => 4
        ]);
        $this->assertEquals(12, $output);
        // Convert strings to numbers for mathematical operations
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('a.toNumber() + b', [
            'a' => '5',
            'b' => 4
        ]);
        $this->assertEquals(9, $output);
    }
}