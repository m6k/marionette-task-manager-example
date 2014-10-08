<?php

namespace Tm;

interface TaskStorage
{
	public function listOpen();
	public function loadById($id);
	public function trackTime(Task $task, TaskTime $time);
	public function taskTrackedTime(Task $task);
	public function create(Task $task);
	public function save(Task $task);
}