<?php

namespace Swiftmade\FEL\Optimizers;

use Swiftmade\FEL\Contracts\OptimizerContract;
use Swiftmade\FEL\Token;
use Swiftmade\FEL\TokenStream;

class InlineIf implements OptimizerContract
{
    /**
     * @var Token
     */
    protected $if;
    /**
     * @var Token
     */
    protected $bracket;
    /**
     * @var Token
     */
    protected $instruction;

    public function optimize(TokenStream $stream, array &$tokens)
    {
        if ($stream->current->type !== Token::EXPRESSION_TYPE || $stream->remaining() <= 2) {
            return false;
        }

        $this->retrieveTokens($stream);

        if (!$this->hasConditional() or !$this->hasNoFollowingBracket()) {
            $stream->rewind(2);
            return false;
        }

        $stream->rewind();
        $tokens[] = new Token(Token::CONTROL_TYPE, $this->if->value, $this->if->cursor);
        $tokens[] = new Token(Token::PUNCTUATION_TYPE, '{', $this->if->cursor + 1);
        $tokens[] = new Token(Token::EXPRESSION_TYPE, $this->instruction->value, $this->instruction->cursor);
        $tokens[] = new Token(Token::PUNCTUATION_TYPE, '}', $this->if->cursor + 2);
        return true;
    }
    
    protected function retrieveTokens(TokenStream $stream)
    {
        $this->instruction = $stream->current;
        $stream->next();
        $this->if = $stream->current;
        $stream->next();
        $this->bracket = $stream->current;
    }

    protected function hasConditional()
    {
        return $this->if->type === Token::CONTROL_TYPE && substr($this->if->value, 0, 2) === 'if';
    }

    protected function hasNoFollowingBracket()
    {
        return !$this->bracket->test(Token::PUNCTUATION_TYPE, '{');
    }
}