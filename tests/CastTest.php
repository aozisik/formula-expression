<?php

use Swiftmade\FEL\FormulaExpression;

class CastTest extends TestCase
{
    public function testItDoesntCastVariablesBetweenLines()
    {
        $evaluator = new FormulaExpression();
        $output = $evaluator->evaluate('abc = _.str("t,e,s,t");def = abc.split(",");def');
        $this->assertEquals(['t', 'e', 's', 't'], $output);
    }
}