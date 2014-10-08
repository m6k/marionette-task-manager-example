<?php


$container = require __DIR__ .'/../bootstrap.php';
$config = $container->config;
$tasks = $container->taskStorage;
$readmeHtml = \Michelf\Markdown::defaultTransform(
	file_get_contents($container->rootDir.'/README.md')
);


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
<link rel="stylesheet" href="/style.css" type="text/css">
</head>

<body>

<h1>Task Manager</h1>

<div id="content"></div>

<script type="text/template" id="tasksView">
	<p><a href="#create">New task</a>
		<a href="#readme">View readme</a>

	<h2>Open Tasks</h2>

	<ul class="taskList"></ul>
</script>


<script type="text/template" id="taskView">
	<li><a href="#tasks/<%- id %>"><%- title %></a>
		<button class="edit">edit</button>
		<button class="track">track time</button>
		<button class="close">close</button>
</script>


<script type="text/template" id="noTasksView">
	No open tasks.
</script>

<script type="text/template" id="taskDetailView">
	<p><a href="#tasks">Back to open tasks</a>

	<h2>Task: <%- title %></h2>

	<%= contentHtml %>

	<p><button class="edit">edit</button>
		<button class="track">track time</button>
		<button class="close">close</button>

	<table class="timeTable loading">
		<caption>Tracked time</caption>
		<thead>
			<tr><th>Date</th><th>Hours</th>
		<tbody class="time">
	</table>

	<p class="loadingInfo">Loading tracked time...
</script>


<script type="text/template" id="taskTimeView">
	<td><%- date %>
	<td><%- hours %>
</script>


<script type="text/template" id="taskTimeEmptyView">
	<td colspan="2" class="emptyTimeView">No time tracked.
</script>


<script type="text/template" id="editTaskView">
	<p><a href="#tasks">Back to open tasks</a>

	<h2><%- pageTitle %></h2>

	<p><label>Task title:
		<input size="60" type="text" class="title"
			value="<%- title %>">
	<p><label>Content:
		<br><textarea rows="5" cols="60" class="content"><%- content %></textarea>
		</label>

	<p><button class="save"><%- action %></button>
</script>


<script type="text/template" id="taskTrackView">
	<p><a href="#tasks">Back to open tasks</a>
		<a href="#tasks/<%- id %>">Back to: <%- title %></a>

	<h2>Track time for: <%- title %></h2>

	<p><label>Date: <input type="text" value="<%- date %>"></label>
		<br><label>Number of hours: <input type="text" value="1"</label>

	<p><button class="track">Track</button>
</script>


<script type="text/template" id="readmeView">
	<p><a href="#tasks">Back to open tasks</a>

	<%= readmeHtml %>
</script>


<script>
Tm.start(<?= json_encode([
	'apiUrl' => $config['apiUrl'],
	'tasks' => $tasks->listOpen(),
	'readmeHtml' => $readmeHtml,
]); ?>);
</script>

</body>
</html>
