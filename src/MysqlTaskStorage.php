<?php

namespace Tm;

class MysqlTaskStorage implements TaskStorage
{
	private $db;

	public function __construct(\PDO $db)
	{
		$this->db = $db;
	}


	private function taskFromRow($row)
	{
		return new Task(array(
				'id' => $row['id'],
				'title' => $row['title'],
				'content' => $row['content'],
				'totalHours' => (int)$row['totalHours'],
				'status' => $row['status'],
		));
	}


	private function queryTasks($whereSql = '', array $whereArgs = array())
	{
		$res = $this->db->prepare("
			SELECT tasks.*, COALESCE(SUM(hours), 0) AS totalHours
			FROM tasks
			LEFT JOIN taskTime ON tasks.id = taskId
			WHERE $whereSql
			GROUP BY tasks.id
			ORDER BY tasks.id"
		);

		$res->execute($whereArgs);

		return $res;
	}

	public function listOpen()
	{
		$result = $this->queryTasks(
			'status = :status',
			array(':status' => Task::OPEN)
		);

		$tasks = array();
		while ($row = $result->fetch()) {
			$tasks[] = $this->taskFromRow($row);
		}

		return $tasks;
	}


	public function loadById($id)
	{
		$row = $this->queryTasks(
			'tasks.id = :id',
			array(':id' => $id)
		)->fetch();
		if (!$row) {
			return $row;
		}
		return $this->taskFromRow($row);
	}


	public function trackTime(Task $task, TaskTime $time)
	{
		$res = $this->db->prepare('
			INSERT INTO taskTime (taskId, date, hours)
			VALUES (:taskId, :date, :hours)');
		$res->execute(array(
			':taskId' => $task->id,
			':date' => $time->date,
			':hours' => $time->hours,
		));

		$loaded = $this->loadById($task->id);

		$task->totalHours = $loaded->totalHours;

		return $task;
	}

	public function taskTrackedTime(Task $task)
	{
		$res = $this->db->prepare('
			SELECT *
			FROM taskTime
			WHERE taskId = :taskId
			ORDER BY id'
		);
		$res->execute(array(':taskId' => $task->id));

		$tracked = array();
		while ($row = $res->fetch()) {
			$tracked[] = new TaskTime(array(
				'date' => $row['date'],
				'hours' => (int)$row['hours'],
			));
		}
		return $tracked;
	}


	public function create(Task $task)
	{
		$res = $this->db->prepare('
			INSERT INTO tasks (title, content, status)
			VALUES (:title, :content, :status)');
		$res->execute(array(
			':title' => $task->title,
			':content' => $task->content,
			':status' => $task->status,
		));

		$task->id = $this->db->lastInsertId();

		return $task;
	}


	public function save(Task $task)
	{
		$res = $this->db->prepare('
			UPDATE tasks SET
				title = :title,
				content = :content,
				status = :status
			WHERE id = :id');
		$res->execute(array(
			':id' => $task->id,
			':title' => $task->title,
			':content' => $task->content,
			':status' => $task->status,
		));
	}

}
