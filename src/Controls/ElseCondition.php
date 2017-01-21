<?php

namespace Swiftmade\FEL\Controls;

use Swiftmade\FEL\Parser;
use Swiftmade\FEL\Token;
use Swiftmade\FEL\TokenStream;
use Swiftmade\FEL\Contracts\ControlContract;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ElseCondition implements ControlContract
{
    /**
     * @var Token
     */
    protected $previousToken;
    protected $previousResult;

    public function lastResult(array $result)
    {
        $this->previousToken = $result['token'];
        $this->previousResult = $result['result'];
    }

    public function run($directive, array $tokens, array &$names)
    {
        if (!$this->previousToken->test(Token::CONTROL_TYPE, '*if|*')) {
            // TODO: Throw syntax error
            return Parser::SKIP;
        }
        if ($this->previousResult !== Parser::IF_FALSE) {
            return Parser::SKIP;
        }

        $expressionLanguage = new ExpressionLanguage();
        if (!$expressionLanguage->evaluate($directive, $names)) {
            return Parser::IF_FALSE;
        }

        $parser = new Parser();
        $result = $parser->parse(new TokenStream($tokens), $names);
        $names = $parser->names;
        return $result;
    }
}

