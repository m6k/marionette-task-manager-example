<?php

namespace Tm;

require __DIR__ .'/../bootstrap.php';

$container = new Container(loadConfig());
enableTracy($container);

$app = new \Slim\Slim();
$app->view(new JsonView());

$tasks = $container->taskStorage;


$app->get('/tasks', function () use ($app, $tasks) {

	$app->render(200, $tasks->listOpen());
});


$app->post('/tasks', function () use ($app, $tasks) {

	$data = json_decode($app->request->getBody(), true);

	$app->render(200, (array)$tasks->create(new \Task($data)));
});


$app->put('/tasks/:id', function ($id) use ($app, $tasks) {

	$data = json_decode($app->request->getBody(), true);

	$app->render(200, (array)$tasks->save(new \Task($data)));
});


$app->get('/tasks/:id/time', function ($id) use ($app, $tasks) {
	$task = $tasks->loadById($id);
	if (!$task) {
		return $app->render(404);
	}

	$app->render(200, $tasks->taskTrackedTime($task));
});


$app->post('/tasks/:id/time', function ($id) use ($app, $tasks) {

	$data = json_decode($app->request->getBody(), true);
	if (!array_key_exists('date', $data) || !array_key_exists('hours', $data)) {
		return $app->render(400, array('message' => 'invalid input'));
	}
	$data['hours'] = (int)$data['hours'];
	$taskTime = new TaskTime($data);

	$task = $tasks->loadById($id);
	if (!$task) {
		return $app->render(404);
	}

	$tasks->trackTime($task, $taskTime);

	$app->render(200, (array)$task); // return modified task data
});


$app->run();
