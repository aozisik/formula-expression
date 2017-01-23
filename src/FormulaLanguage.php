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

    protected function hash($code, array $variables)
    {
        return sha1(serialize($code) . serialize($variables));
    }

    public function evaluate($code, array $variables = [])
    {
        $cacheHash = $this->hash($code, $variables);
        if($this->cache->has($cacheHash)) {
            return $this->cache->get($cacheHash);
        }
        $rawResult = $this->parser->parse(
            $this->lexer->tokenize($code),
            $variables
        );
        $result = $this->optimizeResult($rawResult);
        $this->cache->put($cacheHash, $result);
        return $result;
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