<?php

$container = require __DIR__ .'/../bootstrap.php';

$app = new \Slim\Slim();

$app->view(new Tm\JsonView());

$tasks = $container->taskStorage;


$app->get('/tasks', function () use ($app, $tasks) {

	$app->render(200, $tasks->listOpen());

});


$app->post('/tasks', function () use ($app, $tasks) {

	$data = json_decode($app->request->getBody(), true);

	$app->render(200, (array)$tasks->create(new \Tm\Task($data)));

});

$app->put('/tasks/:id', function ($id) use ($app, $tasks) {

	$data = json_decode($app->request->getBody(), true);

	$app->render(200, (array)$tasks->save(new \Tm\Task($data)));
});




$app->run();
