<?php

namespace Tm;

use Tracy\Debugger;

require_once __DIR__ .'/vendor/autoload.php';

function readConfigFile($name)
{
	return json_decode(file_get_contents(__DIR__ . "/conf/$name.json"), true);
}

/**
 * Minimalistic config loading
 *
 * Use "environment" key conf/local.json to choose environment: devel, live
 *
 * Config structures cannot be nested!
 */
function loadConfig()
{
	$local = readConfigFile('local');
	$environment = readConfigFile($local['environment']);
	$common = readConfigFile('config');

	return array_merge($common, $environment, $local);
}

function enableTracy(Container $container)
{
	Debugger::enable($container->devel
		? Debugger::DEVELOPMENT
		: Debugger::PRODUCTION
	);
}
