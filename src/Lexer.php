<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\ExpressionLanguage\TokenStream;

class Lexer
{
    protected $buffer;
    protected $bufferCursor;

    protected $cursor;
    protected $tokens;
    protected $brackets;

    protected function optimizeExpression($expression)
    {
        return str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $expression);
    }

    protected function buffer($chars)
    {
        if (empty($this->buffer)) {
            $this->bufferCursor = $this->cursor;
        }
        $this->buffer .= $chars;
        $this->cursor += strlen($chars);
    }

    protected function resetBuffer()
    {
        $this->buffer = '';
        $this->bufferCursor = null;
    }

    public function tokenize($expression)
    {
        $expression = str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $expression);

        $this->cursor = 0;
        $this->buffer = '';
        $this->tokens = [];
        $this->brackets = [];
        $end = strlen($expression);

        while ($this->cursor < $end) {

            $char = $expression[$this->cursor];

            if (' ' == $char) {
                // TODO: Possibly append the whitespace to buffer?
                ++$this->cursor;
                continue;
            }

            if (preg_match('/(if|foreach)(\s?)\((?:(?!\{).)*\)/A', $expression, $match, null, $this->cursor)) {
                $tokens[] = new Token(Token::CONTROL_TYPE, $match[1], $this->cursor + 1);
                $this->cursor += strlen($match[0]);
            } elseif (false !== strpos('([{', $char)) {
                // opening bracket
                $brackets[] = array($char, $this->cursor);
                if (empty($buffer)) {
                    $tokens[] = new Token(Token::PUNCTUATION_TYPE, $char, $this->cursor + 1);
                } else {
                    $buffer .= $char;
                }
                ++$this->cursor;
            } elseif (false !== strpos(')]}', $char)) {
                // closing bracket
                if (empty($brackets)) {
                    throw new SyntaxError(sprintf('Unexpected "%s"', $char), $this->cursor);
                }

                list($expect, $cur) = array_pop($brackets);
                if ($char != strtr($expect, '([{', ')]}')) {
                    throw new SyntaxError(sprintf('Unclosed "%s"', $expect), $cur);
                }

                if (empty($buffer)) {
                    $tokens[] = new Token(Token::PUNCTUATION_TYPE, $char, $this->cursor + 1);
                } else {
                    $buffer .= $char;
                }
                ++$this->cursor;
            } elseif (preg_match('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As', $expression, $match, null, $this->cursor)) {
                // strings
                $this->buffer($match[0]);
            } elseif ($char === ';') {
                $tokens[] = new Token(Token::EXPRESSION_TYPE, $this->buffer, $this->cursor + 1);
                $this->resetBuffer();
                ++$this->cursor;
            } else {
                // unlexable
                $this->buffer($char);
            }
        }

        if (!empty($brackets)) {
            list($expect, $cur) = array_pop($brackets);
            throw new SyntaxError(sprintf('Unclosed "%s"', $expect), $cur);
        }

        if (!empty($this->buffer)) {
            $tokens[] = new Token(Token::EXPRESSION_TYPE, $this->buffer, $this->cursor + 1);
            ++$this->cursor;
        }

        $tokens[] = new Token(Token::EOF_TYPE, null, $this->cursor + 1);
        return new TokenStream($tokens);
    }
}
