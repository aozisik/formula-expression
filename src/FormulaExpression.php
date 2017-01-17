<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Filters\BlockIf;
use Swiftmade\FEL\Filters\InlineIf;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FormulaExpression
{
    const SKIP = '_$$skip$$_';

    protected $expressionEngine;
    protected $filters;

    public function __construct()
    {
        $this->expressionEngine = new ExpressionLanguage();
        $this->filters = [
            new BlockIf,
            new InlineIf,
        ];
    }

    protected function evaluateLine($line, array $context)
    {
        foreach ($this->filters as $filter) {
            $matches = [];
            if (preg_match($filter->pattern(), $line, $matches)) {
                return $filter->process($this, $matches, $context);
                break;
            }
        }
        return $this->expressionEngine->evaluate($line, $context);
    }

    protected function removeNewLines($code)
    {
        return str_replace("\n", '', $code);
    }

    public function optimize($code)
    {
        $code = $this->removeNewLines($code);
        return $code;
    }

    public function evaluate($code, array $variables = [])
    {
        $code = $this->optimize($code);

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