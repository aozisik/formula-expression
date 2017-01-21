<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Filters\BlockForeach;
use Swiftmade\FEL\Filters\BlockIf;
use Swiftmade\FEL\Filters\InlineIf;
use Swiftmade\FEL\Filters\SetVariable;
use Swiftmade\FEL\Contracts\FilterContract;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FormulaLanguage
{
    const SKIP = '_$$skip$$_';

    protected $lexer;
    protected $parser;
    protected $tuneOutput;
    protected $filters = [];
    protected $expressionEngine;

    public function __construct($tuneOutput = true)
    {
        $this->lexer = new Lexer();
        $this->parser = new Parser();

        $this->tuneOutput = $tuneOutput;
        $this->expressionEngine = new ExpressionLanguage();
        $this->registerDefaultFilters();
    }

    protected function registerDefaultFilters()
    {
        $this->addFilter(new BlockIf);
        $this->addFilter(new InlineIf);
        $this->addFilter(new SetVariable);
        $this->addFilter(new BlockForeach);
    }

    protected function removeNewLines($code)
    {
        return str_replace("\n", '', $code);
    }

    public function addFilter(FilterContract $filter)
    {
        array_push($this->filters, $filter);
    }

    public function optimize($code)
    {
        $code = $this->removeNewLines($code);
        return $code;
    }

    public function evaluate($code, array $variables = [])
    {

        if ($this->tuneOutput) {
            $this->parser->parse($this->lexer->tokenize($code), $variables);
        }

        $code = $this->optimize($code);

        if (!isset($variables['_'])) {
            $variables['_'] = new Helper($variables);
        }

        $result = null;
        $lines = explode(';', $code);
        foreach ($lines as $line) {
            $result = $this->evaluateLine($line, $variables);
            if ($result === FormulaLanguage::SKIP) {
                $result = null;
                continue;
            } else {
                return $this->output($result);
            }
        }

        return $this->output($result);
    }

    protected function output($result)
    {
        if (!$this->tuneOutput) {
            return $result;
        }
        return (new Result($result))->output();
    }

    protected function evaluateLine($line, array &$context)
    {
        foreach ($this->filters as $filter) {
            $matches = [];
            if (preg_match($filter->pattern(), $line, $matches)) {
                return $filter->process(new self(false), $matches, $context);
                break;
            }
        }
        return $this->expressionEngine->evaluate($line, $context);
    }
}