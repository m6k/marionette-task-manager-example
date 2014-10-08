<?php

namespace Tm;

class RedisTaskStorage
{
	const KEY_TASKS = 'Tasks';
	const KEY_NEXT_TASK_ID = 'NextTaskId';

	private $predis;

	public function __construct(\Predis\Client $predis)
	{
		$this->predis = $predis;
	}


	public function listOpen()
	{
		$raw = $this->predis->hgetall(self::KEY_TASKS);

		$tasks = array();
		foreach ($raw as $json) {
			$task = new Task(json_decode($json, true));

			if ($task->status === Task::OPEN) {
				$tasks[] = $task;
			}
		}

		return $tasks;
	}


	public function create(Task $task)
	{
		$task->id = $this->predis->incr(self::KEY_NEXT_TASK_ID);

		return $this->save($task);
	}


	public function save(Task $task)
	{
		$this->predis->hset(self::KEY_TASKS, $task->id, json_encode($task));

		return $task;
	}

}
