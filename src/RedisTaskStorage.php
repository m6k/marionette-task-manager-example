<?php

namespace Tm;

class RedisTaskStorage
{
	const KEY_TASKS = 'Tasks';
	const KEY_TASK_HOURS = 'TaskHours:';
	const KEY_NEXT_TASK_ID = 'NextTaskId';
	const KEY_TASK_TIME = 'TaskTime:';

	private $predis;

	public function __construct(\Predis\Client $predis)
	{
		$this->predis = $predis;
	}


	private function loadTaskHours(Task $task)
	{
		$task->totalHours = (int)$this->predis->get(self::KEY_TASK_HOURS . $task->id);
		return $task;
	}


	public function listOpen()
	{
		$raw = $this->predis->hgetall(self::KEY_TASKS);

		$tasks = array();
		foreach ($raw as $json) {
			$task = new Task(json_decode($json, true));

			// oh the efficiency, remote call in a loop
			$this->loadTaskHours($task);

			if ($task->status === Task::OPEN) {
				$tasks[] = $task;
			}
		}

		return $tasks;
	}


	public function loadById($id)
	{
		$json = $this->predis->hget(self::KEY_TASKS, $id);
		if ($json === null) {
			return null;
		}

		return $this->loadTaskHours(new Task(json_decode($json)));
	}


	public function trackTime(Task $task, TaskTime $time)
	{
		$this->predis->lpush(
			self::KEY_TASK_TIME . $task->id,
			json_encode($time)
		);

		$task->totalHours = (int)$this->predis->incrBy(
			self::KEY_TASK_HOURS . $task->id,
			$time->hours
		);

		return $task;
	}

	public function taskTrackedTime(Task $task)
	{
		$raw = $this->predis->lrange(
			self::KEY_TASK_TIME . $task->id,
			0,
			-1 // get all
		);

		$tracked = array();
		foreach ($raw as $json) {
			$tracked[] = new TaskTime(json_decode($json, true));
		}
		return $tracked;
	}


	public function create(Task $task)
	{
		$task->id = $this->predis->incr(self::KEY_NEXT_TASK_ID);

		return $this->save($task);
	}


	public function save(Task $task)
	{
		$data = (array)$task;
		unset($data['totalHours']); // totalHours is computed, do not save it

		$this->predis->hset(self::KEY_TASKS, $task->id, json_encode($data));

		return $task;
	}

}
