<?php

namespace Tm;

require_once __DIR__ .'/TaskStorageTestBase.php';

class MysqlTaskStorageTest extends TaskStorageTestBase
{
	private $db;

	public function setUp()
	{
		$container =  new Container(loadConfig());

		$this->db = $container->db;
		$this->tasks = $container->mysqlTaskStorage;

		if (!$container->devel) {
			throw new \Exception("Tests can run only on devel environment, they clear data");
		}

		$this->db->query('TRUNCATE taskTime');
		$this->db->query('TRUNCATE tasks');
	}

}

