<?php

use Swiftmade\FEL\FormulaLanguage;

class CastTest extends TestCase
{
    public function testItDoesntCastVariablesBetweenLines()
    {
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('abc = _.str("t,e,s,t");def = abc.split(",");def');
        $this->assertEquals(['t', 'e', 's', 't'], $output);
    }

    public function testItCastsVariablesBack()
    {
        $evaluator = new FormulaLanguage();
        $output = $evaluator->evaluate('abc = _.str("t,e,s,t");def = abc.split(",");_.collect(def)');
        $this->assertEquals('array', gettype($output));
    }
}