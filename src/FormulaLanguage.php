<?php

namespace Swiftmade\FEL;

class FormulaLanguage
{

    protected $lexer;
    protected $parser;

    public function __construct()
    {
        $this->lexer = new Lexer();
        $this->parser = new Parser();
    }

    public function evaluate($code, array $variables = [])
    {
        return $this->parser->parse(
            $this->lexer->tokenize($code),
            $variables
        );
    }
}