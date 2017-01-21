<?php

namespace Swiftmade\FEL;

use Symfony\Component\ExpressionLanguage\SyntaxError;

class TokenStream
{
    public $current;

    private $tokens;
    private $position = 0;

    /**
     * Constructor.
     *
     * @param array $tokens An array of tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->current = $tokens[0];
    }

    /**
     * Returns a string representation of the token stream.
     *
     * @return string
     */
    public function __toString()
    {
        return implode("\n", $this->tokens);
    }

    /**
     * Sets the pointer to the next token and returns the old one.
     */
    public function next()
    {
        if (!isset($this->tokens[$this->position])) {
            throw new SyntaxError('Unexpected end of expression', $this->current->cursor);
        }

        ++$this->position;

        $this->current = $this->tokens[$this->position];
    }

    /**
     * Tests a token.
     *
     * @param array|int $type The type to test
     * @param string|null $value The token value
     * @param string|null $message The syntax error message
     */
    public function expect($type, $value = null, $message = null)
    {
        $token = $this->current;
        if (!$token->test($type, $value)) {
            throw new SyntaxError(sprintf('%sUnexpected token "%s" of value "%s" ("%s" expected%s)', $message ? $message . '. ' : '', $token->type, $token->value, $type, $value ? sprintf(' with value "%s"', $value) : ''), $token->cursor);
        }
        $this->next();
    }

    /**
     * Checks if end of stream was reached.
     *
     * @return bool
     */
    public function isEOF()
    {
        return $this->current->type === Token::EOF_TYPE;
    }

    public function reset()
    {
        $this->position = 0;
        $this->current = $this->tokens[$this->position];
    }

    public function rewind($step = 1)
    {
        $this->position -= $step;
        $this->current = $this->tokens[$this->position];
    }

    public function remaining()
    {
        return count($this->tokens) - $this->position + 1;
    }
}
