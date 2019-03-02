<?php

/**
 * DIC configuration
 */

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

$container = $app->getContainer();

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

$container['db'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['database'];
    $pdo = new \PDO($settings['dsn'], $settings['username'], $settings['password'], $settings['attributes']);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS employees (
            name VARCHAR(128) NOT NULL,
            supervisor VARCHAR(128) NULL,
            PRIMARY KEY (name),
            FOREIGN KEY (supervisor) REFERENCES employees(name)
        );'
    );
	
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            username VARCHAR(128) NOT NULL,
            password VARCHAR(128) NOT NULL,
            PRIMARY KEY (username)
        );'
    );
	
	// The follow is purely for testing purposes.
	$superuser = $c->get('settings')['system'];
	$username = $superuser['username'];
	$password = password_hash($superuser['password'], PASSWORD_DEFAULT);
	$sql = sprintf(
		"INSERT INTO users (username, password) VALUES ('%s','%s') ",
		$username,
		$password
	);
	$pdo->exec('DELETE FROM users');
	$pdo->exec($sql);
    return $pdo;
};

$container['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, \Throwable $exception) use ($c) {
        return $response->withJson(
            [
                'success' => false,
                'message' => $exception->getMessage(),
                'exception_type' => get_class($exception),
            ], 500);
    };
};

$container['notFoundHandler'] = function ($c) {
    return function (Request $request, Response $response) use ($c) {
        return $response->withJson(
            [
                'success' => false,
                'message' => 'Endpoint not found',
            ], 404);
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function (Request $request, Response $response, array $methods) use ($c) {
        return $response->withJson(
            [
                'success' => false,
                'message' => 'Method not allowed',
                'methods_allowed' => $methods,
            ], 405)
            ->withHeader('Allow', implode(', ', $methods));
    };
};

$container['phpErrorHandler'] = function ($c) {
    return function (Request $request, Response $response, \Throwable $throwable) use ($c) {
        return $response->withJson(
            [
                'success' => false,
                'message' => $throwable->getMessage(),
                'exception_type' => get_class($throwable),
            ], 500);
    };
};