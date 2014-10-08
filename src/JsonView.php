<?php

namespace Tm;

class JsonView extends \Slim\View {

    public function render($status=200, $data = NULL)
    {
        $app = \Slim\Slim::getInstance();

        $app->response->setStatus(intval($status));
        $app->response()->header('Content-Type', 'application/json');

        $data = $this->all();
        unset($data['flash']);

		$app->response()->body(json_encode($data));

        $app->stop();
    }

}
