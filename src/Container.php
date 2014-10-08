<?php

namespace Tm;

use Tracy\Debugger;

/**
 * Dependency Injection container, used to compose dependency tree and create services
 */
class Container
{

	public function __construct($config)
	{
		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}

	/**
	 * Lazyloading of services, the magic is here
	 */
	public function __get($name)
	{
		if (!$this->serviceExists($name)) {
			throw new \Exception("Service $name not exists");
		}
		return $this->$name = $this->{"new$name"}();
	}


	public function serviceExists($name)
	{
		return isset($this->$name) || method_exists($this, "new$name");
	}

	protected function newTaskStorage()
	{
		return $this->{$this->taskStorageService};
	}


	protected function newPredis()
	{
		return new \Predis\Client($this->redisUri);
	}


	protected function newRedisTaskStorage()
	{
		return new RedisTaskStorage($this->predis);
	}


}
