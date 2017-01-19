<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\Token as SymfonyToken;

class Token extends SymfonyToken
{
    const LINE_BREAK = 'linebreak';
    const CONTROL_TYPE = 'control';
    const EXPRESSION_TYPE = 'expression';
    const ASSIGNMENT_TYPE = 'assignment';
}