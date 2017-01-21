<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\ExpressionLanguage\TokenStream;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Parser
{
    const SKIP = '_$$skip$$_';

    protected $names;
    protected $stream;
    protected $evaluator;

    protected function enhanceNames()
    {
        if (!isset($this->names['_'])) {
            $this->names['_'] = new Helper();
        }
    }

    public function parse(TokenStream $tokenStream, array $names)
    {
        $this->names = $names;
        $this->stream = $tokenStream;
        $this->enhanceNames();

        $returnableValue = Parser::SKIP;

        $expectedTypes = [
            Token::ASSIGNMENT_TYPE,
            Token::CONTROL_TYPE,
            Token::EXPRESSION_TYPE
        ];

        while ($returnableValue === Parser::SKIP) {
            $token = $this->stream->current;
            if (!in_array($token->type, $expectedTypes)) {
                throw new SyntaxError("Unexpected token", $token->cursor);
            }

            switch ($token->type) {
                case Token::ASSIGNMENT_TYPE:
                    $returnableValue = $this->handleAssignment($token);
                    break;
                case Token::EXPRESSION_TYPE:
                    $returnableValue = $this->evaluate($token->value, $this->names);
                    break;
                case Token::CONTROL_TYPE:
                    $returnableValue = $this->handleControl($token);
                    break;
            }

            if ($this->stream->isEOF()) {
                break;
            }
            $this->stream->next();
        }

        return $returnableValue;
    }

    protected function evaluate($expression, array $names = [])
    {
        if (!isset($this->evaluator)) {
            $this->evaluator = new ExpressionLanguage();
        }
        return $this->evaluator->evaluate($expression, $names);
    }

    protected function handleAssignment($token)
    {
        $params = explode('=', $token->value);
        $this->names[$params[0]] = $this->evaluate($params[1], $this->names);
        return Parser::SKIP;
    }

    protected function handleControl($token)
    {
        return Parser::SKIP;
    }
}