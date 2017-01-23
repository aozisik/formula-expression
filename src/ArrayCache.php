<?php

namespace Swiftmade\FEL;

use Swiftmade\FEL\Contracts\CacheDriver;

class ArrayCache implements CacheDriver
{
	protected $array = [];

	public function forget($key)
	{
		if($this->has($key)) {
			unset($this->array[$key]);
		}
	}
	
	public function put($key, $value)
	{
		$this->array[$key] = $value;
	}

	public function get($key, $default = null)
	{
		if(!$this->has($key)) {
			return $default;
		}
		return $this->array[$key];
	}

	public function has($key)
	{
		return isset($this->array[$key]);
	}
}