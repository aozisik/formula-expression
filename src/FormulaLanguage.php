<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Casts\Stringy;
use Swiftmade\FEL\Casts\Collection;
use Swiftmade\FEL\Contracts\CastContract;

class FormulaLanguage
{

    protected $lexer;
    protected $parser;
    protected $typeCasts;

    public function __construct()
    {
        $this->typeCasts = [];
        $this->lexer = new Lexer();
        $this->parser = new Parser();

        $this->registerTypeCast(new Stringy);
        $this->registerTypeCast(new Collection);
    }

    public function registerTypeCast(CastContract $cast)
    {
        $this->typeCasts[$cast->type()] = $cast;
    }

    public function evaluate($code, array $variables = [])
    {
        $result = $this->parser->parse(
            $this->lexer->tokenize($code),
            $variables
        );

        return $this->returnResult($result);
    }

    protected function resultType($result)
    {
        $type = gettype($result);
        if ($type === 'object') {
            return get_class($result);
        }
        return $type;
    }

    protected function returnResult($result)
    {
        $resultType = $this->resultType($result);
        if (isset($this->typeCasts[$resultType])) {
            return $this->typeCasts[$resultType]->cast($result);
        }
        return $result;
    }
}