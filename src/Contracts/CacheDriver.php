<?php

namespace Swiftmade\FEL\Contracts;

interface CacheDriver
{
	public function forget($key);
	
	public function put($key, $value);

	public function get($key, $default = null);

	public function has($key);
}