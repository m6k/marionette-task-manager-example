<?php

$container = require __DIR__ .'/../bootstrap.php';
$config = $container->config;
$tasks = $container->taskStorage;

?><!DOCTYPE html>
<html>
<head>
	<title>Task Manager</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
	foreach ([
		'/bower/jquery/dist/jquery.js',
		'/bower/underscore/underscore.js',
		'/bower/backbone/backbone.js',
		'/bower/backbone.marionette/lib/backbone.marionette.js',
		'/app.js',
	] as $lib) {
		echo "\t\t<script src='$lib'></script>";
	}
?>

</head>

<body>

<h1>Task Manager</h1>

<div id="content"></div>

<script type="text/template" id="tasksView">
	<p><a href="#create">New task</a>

	<h2>Open Tasks</h2>

	<ul class="tasks"></ul>
</script>


<script type="text/template" id="editTaskView">
	<p><a href="/tasks">Back to open tasks</a>

	<h2><%- action %></h2>

	<p><label for="<%- cid %>TaskTitle">Task title:</label>
		<input size="60" type="text" id="<%- cid %>TaskTitle" class="title"
			value="<%- title %>">
	<p><label for="<%- cid %>TaskContent">Content:</label>
	<br><textarea id="<%- cid %>TaskContent"
		rows="5" cols="60" class="content"><%- content %></textarea>

	<p><button class="save"><%- action %></button>
</script>

<script type="text/template" id="taskView">
	<li><a href="/tasks/<%- id %>"><%- title %></a>
		<!--<button class="edit">edit</button>
		<button class="track">track</button>-->
		<button class="close">close</button>
</script>

<script>
Tm.start(<?= json_encode([
	'apiUrl' => $config['apiUrl'],
	'tasks' => $tasks->listOpen(),
]); ?>);
</script>

</body>
</html>
