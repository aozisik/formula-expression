<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Controls\Each;
use Swiftmade\FEL\Controls\Condition;

use Swiftmade\FEL\Optimizers\InlineIf;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Parser
{
    const SKIP = '_$$skip$$_';

    public $names;
    /**
     * @var TokenStream
     */
    protected $stream;
    protected $evaluator;
    protected $controls = [];
    protected $optimizers = [];

    public function __construct()
    {
        $this->controls = [
            'foreach' => new Each,
            'if' => new Condition,
        ];

        $this->optimizers = [
            new InlineIf
        ];
    }

    protected function enhanceNames()
    {
        if (!isset($this->names['_'])) {
            $this->names['_'] = new Helper();
        }
    }

    protected function optimizeStream(TokenStream $stream)
    {
        foreach ($this->optimizers as $optimizer) {
            $tokens = [];
            while ($stream->current->type !== Token::EOF_TYPE) {
                if (!$optimizer->optimize($stream, $tokens)) {
                    $tokens[] = $stream->current;
                }
                $stream->next();
            }
            $tokens[] = $stream->current;
            $stream = new TokenStream($tokens);
        }
        return $stream;
    }

    public function parse(TokenStream $tokenStream, array $names = null)
    {
        $this->names = $names;
        $this->enhanceNames();
        $this->stream = $this->optimizeStream($tokenStream);

        $returnableValue = Parser::SKIP;

        while ($returnableValue === Parser::SKIP) {
            $token = $this->stream->current;

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
                case Token::EOF_TYPE:
                    break(2);
                default:
                    throw new SyntaxError("Unexpected token", $token->cursor);
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
        $pos = strpos($token->value, '|');
        $command = substr($token->value, 0, $pos);
        $expression = substr($token->value, $pos + 1, strlen($token) - ($pos + 1));

        if (!isset($this->controls[$command])) {
            throw new SyntaxError('Unknown control "' . $command . '"', $token->cursor);
        }

        $tokens = [];
        $control = $this->controls[$command];

        $this->stream->next();
        $this->stream->expect('punctuation', '{');
        $cursor = $this->stream->current->cursor;
        $level = 1;

        while ($level > 0) {
            if ($this->stream->current->test('punctuation', '}')) {
                --$level;
            } else if ($this->stream->current->test('punctuation', '{')) {
                ++$level;
            }
            if ($level === 0) {
                break;
            }
            $tokens[] = $this->stream->current;
            $cursor = $this->stream->current->cursor;
            $this->stream->next();
        }

        $tokens[] = new Token(Token::EOF_TYPE, null, $cursor + 1);
        return $control->run($expression, $tokens, $this->names);
    }
}