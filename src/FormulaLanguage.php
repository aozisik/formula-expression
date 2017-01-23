<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Casts\Stringy;
use Swiftmade\FEL\Casts\Collection;
use Swiftmade\FEL\Contracts\CacheDriver;
use Swiftmade\FEL\Contracts\CastContract;

class FormulaLanguage
{
    protected $cache;
    protected $lexer;
    protected $parser;
    protected $typeCasts;

    public function __construct(CacheDriver $cache = null)
    {
        $this->cache = $cache;
        $this->typeCasts = [];
        $this->lexer = new Lexer();
        $this->parser = new Parser();

        $this->registerTypeCast(new Stringy);
        $this->registerTypeCast(new Collection);

        if(is_null($this->cache)) {
            $this->cache = new ArrayCache();
        }
    }

    public function registerTypeCast(CastContract $cast)
    {
        $this->typeCasts[$cast->type()] = $cast;
    }

    protected function hash($var)
    {
        return sha1(serialize($var));
    }

    protected function remember($key, $value)
    {
        if($this->cache->has($key)) {
            return $this->cache->get($key);
        }
        $result = call_user_func($value);
        $this->cache->put($key, $result);
        return $result;
    }

    public function evaluate($code, array $variables = [])
    {
        $resultHash = sha1($this->hash($code) . $this->hash($variables));

        return $this->remember($resultHash, function() use($code, $variables) {
            $tokenStream = $this->remember(
                'code.' . $this->hash($code),
                function() use($code) {
                    return $this->lexer->tokenize($code);
                }
            );
            $rawResult = $this->parser->parse($tokenStream, $variables);
            return $this->optimizeResult($rawResult);
        });
    }

    protected function resultType($result)
    {
        $type = gettype($result);
        if ($type === 'object') {
            return get_class($result);
        }
        return $type;
    }

    protected function optimizeResult($result)
    {
        $nullValues = [Parser::SKIP, Parser::IF_FALSE];
        if (gettype($result) === 'string' and in_array($result, $nullValues)) {
            return null;
        }
        $resultType = $this->resultType($result);
        if (isset($this->typeCasts[$resultType])) {
            return $this->typeCasts[$resultType]->cast($result);
        }
        return $result;
    }
}