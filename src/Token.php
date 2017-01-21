<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\Token as SymfonyToken;

class Token extends SymfonyToken
{
    const LINE_BREAK = 'linebreak';
    const CONTROL_TYPE = 'control';
    const EXPRESSION_TYPE = 'expression';
    const ASSIGNMENT_TYPE = 'assignment';

    protected function str_is($pattern, $value)
    {
        if ($pattern == $value) return true;
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '.*', $pattern) . '\z';
        return (bool)preg_match('#^' . $pattern . '#', $value);
    }

    public function test($type, $value = null)
    {
        return $this->type === $type && (null === $value || $this->str_is($value, $this->value));
    }
}