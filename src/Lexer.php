<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\SyntaxError;

class Lexer
{
    protected $buffer;
    protected $bufferCursor;

    protected $cursor;
    protected $tokens;

    protected function optimizeExpression($expression)
    {
        $expression = str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $expression);
        if ($expression[strlen($expression - 1)] !== ';') {
            $expression .= ';';
        }
        return $expression;
    }

    protected function buffer($chars)
    {
        if (empty($this->buffer)) {
            $this->bufferCursor = $this->cursor + 1;
        }
        $this->buffer .= $chars;
        $this->cursor += strlen($chars);
    }

    protected function resetBuffer()
    {
        $this->buffer = '';
        $this->bufferCursor = null;
    }

    protected function flushBuffer()
    {
        $buffer = trim($this->buffer);
        $this->resetBuffer();
        if (empty($buffer)) {
            return;
        }
        $this->addToken(new Token(Token::EXPRESSION_TYPE, $buffer, $this->bufferCursor));
    }

    protected function addToken(Token $token)
    {
        if (!empty($this->buffer)) {
            $this->flushBuffer();
        }
        $this->tokens[] = $token;
    }

    public function tokenize($expression)
    {
        $expression = str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $expression);
        $end = strlen($expression);

        $brackets = [];
        $this->cursor = 0;
        $this->tokens = [];
        $this->buffer = '';
        $controlBrackets = 0;
        $bracketLevel = null;

        while ($this->cursor < $end) {

            $char = $expression[$this->cursor];
            if (' ' == $char) {
                // TODO: Possibly append the whitespace to buffer?
                ++$this->cursor;
                continue;
            }

            if (preg_match('/(if|elseif|foreach)\s?\(((?:(?!\{|;).)*)\)/A', $expression, $match, null, $this->cursor)) {
                if (empty($this->buffer)) {
                    // This is not an inline control, so let's expect a bracket
                    $bracketLevel = is_null($bracketLevel) ? 0 : $bracketLevel + 1;
                    ++$controlBrackets;
                }
                $this->addToken(new Token(Token::CONTROL_TYPE, $match[1] . '|' . $match[2], $this->cursor + 1));
                $this->cursor += strlen($match[0]);
            } elseif (false !== strpos('{', $char)) {
                // opening bracket
                $brackets[] = array($char, $this->cursor);
                if (!is_null($bracketLevel)) {
                    if ($controlBrackets > 0) {
                        $this->addToken(new Token(Token::PUNCTUATION_TYPE, '{', $this->cursor + 1));
                    }
                    ++$bracketLevel;
                } else {
                    $this->buffer($char);
                }
                ++$this->cursor;
            } elseif (false !== strpos('}', $char)) {
                // closing bracket
                if (empty($brackets)) {
                    throw new SyntaxError(sprintf('Unexpected "%s"', $char), $this->cursor);
                }
                list($expect, $cur) = array_pop($brackets);
                if ($char != strtr($expect, '{', '}')) {
                    throw new SyntaxError(sprintf('Unclosed "%s"', $expect), $cur);
                }

                if (is_null($bracketLevel)) {
                    $this->buffer($char);
                } else {
                    --$bracketLevel;
                    if ($controlBrackets > 0) {
                        $this->addToken(new Token(Token::PUNCTUATION_TYPE, $char, $this->cursor + 1));
                        --$controlBrackets;
                    }
                }
                ++$this->cursor;
            } else if (preg_match('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s=\s(.*)(;)(?=(?:[^"]|"[^"]*")*$)/AU', $expression, $match, null, $this->cursor)) {
                // Variable assignments
                $this->addToken(new Token(Token::ASSIGNMENT_TYPE, $match[1] . '=' . $match[2], $this->cursor + 1));
                $this->cursor += strlen($match[0]);
            } elseif (preg_match('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As', $expression, $match, null, $this->cursor)) {
                // prevent catching semi colons inside strings
                $this->buffer($match[0]);
            } elseif ($char === ';') {
                $this->flushBuffer();
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

        $this->addToken(new Token(Token::EOF_TYPE, null, $this->cursor + 1));
        return new TokenStream($this->tokens);
    }
}
