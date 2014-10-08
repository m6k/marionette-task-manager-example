<?php

namespace Tm;

class RedisTaskStorageTest extends \PHPUnit_Framework_TestCase
{
	private $tasks, $predis;

	public function setUp()
	{
		$container =  new Container(loadConfig());

		$this->predis = $container->predis;
		$this->tasks = $container->redisTaskStorage;

		if (!$container->devel) {
			throw new \Exception("Tests can run only on devel environment, they clear data");
		}

		$this->predis->flushdb();
	}


	public function testNoTasksInitailly()
	{
		$this->assertSame(0, count($this->tasks->listOpen()));
	}


	public function testCreateTask()
	{
		$task = $this->tasks->create(new Task(array(
			'title' => 'new task',
		)));
		$this->assertSame('new task', $task->title);
		$this->assertSame(0, $task->totalHours);

	}


	public function testLoadTask()
	{
		$task = $this->tasks->create(new Task(array(
			'title' => 'new task',
		)));

		$loaded = $this->tasks->loadById($task->id);

		$this->assertSame('new task', $loaded->title);
		$this->assertSame(0, $loaded->totalHours);
	}


	public function testListCreatedTasks()
	{
		$this->tasks->create(new Task(array(
			'title' => 'a',
		)));
		$this->tasks->create(new Task(array(
			'title' => 'b',
		)));

		$tasks = $this->tasks->listOpen();

		$this->assertSame(2, count($tasks));

		$this->assertSame('a', $tasks[0]->title);
		$this->assertSame(0, $tasks[0]->totalHours);
		$this->assertSame('b', $tasks[1]->title);
	}


	public function testCloseTask()
	{
		$task = $this->tasks->create(new Task(array(
			'title' => 'a',
		)));
		$task->status = Task::CLOSED;
		$this->tasks->save($task);

		$this->assertSame(0, count($this->tasks->listOpen()));
	}


	public function testNewTaskHasNoTrackedTime()
	{
		$task = $this->tasks->create(new Task(array(
			'title' => 'a',
		)));

		$this->assertSame(0, count($this->tasks->taskTrackedTime($task)));
	}


	public function testTrackTimeReturnsModifiedTask()
	{
		$task = $this->tasks->create(new Task(array(
			'title' => 'a',
		)));

		$returned = $this->tasks->trackTime($task, new TaskTime(array(
			'date' => 'd',
			'hours' => 3,
		)));

		$this->assertSame($task, $returned);
		$this->assertSame(3, $returned->totalHours);
	}


	public function testTrackedTimeComputedInTaskList()
	{
		$task = $this->tasks->create(new Task(array(
			'title' => 'a',
		)));

		$this->tasks->trackTime($task, new TaskTime(array(
			'date' => 'd-a',
			'hours' => 3,
		)));
		$this->tasks->trackTime($task, new TaskTime(array(
			'date' => 'd-b',
			'hours' => 2,
		)));

		$tasks = $this->tasks->listOpen();
		$this->assertSame(5, $tasks[0]->totalHours);
	}


	public function testTrackedTimeWhenMoreTasks()
	{
		$taskA = $this->tasks->create(new Task(array(
			'title' => 'a',
		)));
		$taskB = $this->tasks->create(new Task(array(
			'title' => 'a',
		)));

		$this->tasks->trackTime($taskA, new TaskTime(array(
			'date' => 'd-a',
			'hours' => 3,
		)));
		$this->tasks->trackTime($taskB, new TaskTime(array(
			'date' => 'd-b',
			'hours' => 2,
		)));

		$tasks = $this->tasks->listOpen();
		$this->assertSame(3, $tasks[0]->totalHours);
		$this->assertSame(2, $tasks[1]->totalHours);
	}
}

