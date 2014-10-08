<?php

namespace Tm;

class Task extends DataObject
{
	const OPEN = 'open';
	const CLOSED = 'closed';

	public $id;
	public $title;
	public $content;
	public $status = self::OPEN;
	public $totalHours = 0;
}
