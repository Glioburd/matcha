<?php

// Get container
$container = $app->getContainer();

// Register component on container

$container['debug'] = function () {
	return true;
};

// $container['csrf'] = function () {
//	return new \Slim\Csrf\Guard;
// };

$container['db'] = function ($container) {

	$db = \App\models\DBFactory::getMysqlConnexionWithPDO();
	return $db;
};

$container['view'] = function ($container) {
	$dir = dirname(__DIR__);
	$view = new \Slim\Views\Twig($dir . '/app/views', [
		'cache' => $container->debug ? false : $dir . 'tmp/cache',
		'debug' => $container->debug
	]);

	if ($container->debug) {
		$view->addExtension(new Twig_Extension_Debug());
	}
	
	// Instantiate and add Slim specific extension
	$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
	$view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

	return $view;
};	