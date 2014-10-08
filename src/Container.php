<?php

namespace Tm;

use Tracy\Debugger;

/**
 * Dependency Injection container, used to compose dependency tree and create services
 */
class Container
{

	public function __construct()
	{
		if ($this->config['devel']) {
			Debugger::enable($this->config['devel']
				? Debugger::DEVELOPMENT
				: Debugger::PRODUCTION
			);
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


	protected function newRootDir()
	{
		return realpath(__DIR__ .'/../');
	}


	private function readConfig($name)
	{
		return json_decode(file_get_contents($this->rootDir ."/conf/$name.json"), true);
	}


	protected function newConfig()
	{
		$local = $this->readConfig('local');
		$environment = $this->readConfig($local['environment']);
		$common = $this->readConfig('config');

		return array_merge($common, $environment, $local);
	}


	protected function newTaskStorage()
	{
		return $this->{$this->config['taskStorage']};
	}


	protected function newPredis()
	{
		return new \Predis\Client($this->config['redisUri']);
	}


	protected function newRedisTaskStorage()
	{
		return new RedisTaskStorage($this->predis);
	}


}
