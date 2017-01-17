<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FormulaExpression
{
    const SKIP = '_$$skip$$_';

    protected $expressionEngine;

    public function __construct()
    {
        $this->expressionEngine = new ExpressionLanguage();
    }

    protected function evaluateLineWithConditional($line, $conditional, array $context)
    {
        if (!$this->evaluateLine($conditional, $context)) {
            return FormulaExpression::SKIP;
        }
        return $this->evaluateLine($line, $context);
    }

    protected function evaluateLine($line, array $context)
    {
        $matches = [];
        if (preg_match('/(.*) if\((.*)\)/', $line, $matches)) {
            return $this->evaluateLineWithConditional($matches[1], $matches[2], $context);
        }
        return $this->expressionEngine->evaluate($line, $context);
    }

    public function evaluate($code, array $variables = [])
    {
        $result = null;
        $lines = explode(';', $code);
        foreach ($lines as $line) {
            $result = $this->evaluateLine($line, $variables);
            if ($result === FormulaExpression::SKIP) {
                continue;
            }
        }
        return $result;
    }
}