<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\ExpressionLanguage\TokenStream;

class Lexer
{

    public function tokenize($expression)
    {
        $expression = str_replace(array("\r", "\n", "\t", "\v", "\f"), ' ', $expression);
        $cursor = 0;
        $tokens = array();
        $brackets = array();
        $end = strlen($expression);
        $buffer = '';

        while ($cursor < $end) {
            if (' ' == $expression[$cursor]) {
                // TODO: Possibly append the whitespace to buffer?
                ++$cursor;
                continue;
            }

            echo $expression[$cursor] . PHP_EOL;

            if (preg_match('/(if|foreach)(\s?)\((?:(?!\{).)*\)/A', $expression, $match, null, $cursor)) {
                $control = $match[1];
                $directive = $match[3];
                echo $match[0];
                $tokens[] = new Token(Token::CONTROL_TYPE, $match[1], $cursor + 1);
                $cursor += strlen($match[0]);
            } elseif (false !== strpos('([{', $expression[$cursor])) {
                // opening bracket
                $brackets[] = array($expression[$cursor], $cursor);
                if (empty($buffer)) {
                    $tokens[] = new Token(Token::PUNCTUATION_TYPE, $expression[$cursor], $cursor + 1);
                } else {
                    $buffer .= $expression[$cursor];
                }
                ++$cursor;
            } elseif (false !== strpos(')]}', $expression[$cursor])) {
                // closing bracket
                if (empty($brackets)) {
                    throw new SyntaxError(sprintf('Unexpected "%s"', $expression[$cursor]), $cursor);
                }

                list($expect, $cur) = array_pop($brackets);
                if ($expression[$cursor] != strtr($expect, '([{', ')]}')) {
                    throw new SyntaxError(sprintf('Unclosed "%s"', $expect), $cur);
                }

                if (empty($buffer)) {
                    $tokens[] = new Token(Token::PUNCTUATION_TYPE, $expression[$cursor], $cursor + 1);
                } else {
                    $buffer .= $expression[$cursor];
                }
                ++$cursor;
            } elseif (preg_match('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As', $expression, $match, null, $cursor)) {
                // strings
                $buffer .= $match[0];
                $cursor += strlen($match[0]);
            } elseif ($expression[$cursor] === ';') {
                $tokens[] = new Token(Token::EXPRESSION_TYPE, $buffer, $cursor + 1);
                $buffer = '';
                ++$cursor;
            } else {
                // unlexable
                $buffer .= $expression[$cursor];
                ++$cursor;
            }
        }

        $tokens[] = new Token(Token::EOF_TYPE, null, $cursor + 1);

        if (!empty($brackets)) {
            list($expect, $cur) = array_pop($brackets);
            throw new SyntaxError(sprintf('Unclosed "%s"', $expect), $cur);
        }

        if (!empty($this->buffer)) {
            $tokens[] = new Token(Token::EXPRESSION_TYPE, $buffer, $cursor + 1);
        }
        return new TokenStream($tokens);
    }
}
