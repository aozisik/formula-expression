<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Filters\BlockIf;
use Swiftmade\FEL\Filters\InlineIf;
use Swiftmade\FEL\Filters\SetVariable;

use Swiftmade\FEL\Overriders\Stringy;

use Swiftmade\FEL\Contracts\RecastContract;
use Swiftmade\FEL\Contracts\FilterContract;
use Swiftmade\FEL\Contracts\OverriderContract;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class FormulaExpression
{
    const SKIP = '_$$skip$$_';

    protected $expressionEngine;

    protected $filters = [];
    protected $recasts = [];
    protected $overriders = [];

    public function __construct()
    {
        $this->expressionEngine = new ExpressionLanguage();
        $this->registerDefaultFilters();
        $this->registerDefaultOverriders();
    }

    protected function registerDefaultFilters()
    {
        $this->addFilter(new BlockIf);
        $this->addFilter(new InlineIf);
        $this->addFilter(new SetVariable);
    }

    protected function registerDefaultOverriders()
    {
        $this->addOverrider(new Stringy);
    }

    protected function removeNewLines($code)
    {
        return str_replace("\n", '', $code);
    }

    public function addFilter(FilterContract $filter)
    {
        array_push($this->filters, $filter);
    }

    public function addOverrider(OverriderContract $overrider)
    {
        $this->overriders[$overrider->type()] = $overrider;
        if ($overrider instanceof RecastContract) {
            $this->recasts[$overrider->resultType()] = $overrider;
        }
    }

    public function optimize($code)
    {
        $code = $this->removeNewLines($code);
        return $code;
    }

    public function evaluate($code, array $variables = [])
    {
        $code = $this->optimize($code);
        $variables = $this->overrideVariables($variables);

        if (!isset($variables['_'])) {
            $variables['_'] = new Helper();
        }

        $result = null;
        $lines = explode(';', $code);
        foreach ($lines as $line) {
            $result = $this->evaluateLine($line, $variables);
            if ($result === FormulaExpression::SKIP) {
                $result = null;
                continue;
            } else {
                return $this->recast($result);
            }
        }

        return $this->recast($result);
    }

    protected function overrideVariables(array $variables)
    {
        foreach ($variables as $key => $variable) {
            $type = gettype($variable);
            if (array_key_exists($type, $this->overriders)) {
                $variables[$key] = $this->overriders[$type]->override($variable);
            }
        }
        return $variables;
    }

    protected function recast($result)
    {
        $type = gettype($result);
        if ($type == 'object') {
            $type = get_class($result);
        }
        if (array_key_exists($type, $this->recasts)) {
            return $this->recasts[$type]->recast($result);
        }
        return $result;
    }

    protected function evaluateLine($line, array &$context)
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
}