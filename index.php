<?php

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router(URL_BASE);

$router->namespace("App\Controllers");

/*
 * Dashboard
 */
$router->group(null);
$router->get('/', 'BeneficioController:dashboard', 'beneficio.dashboard');

/*
 * Beneficios
 */
$router->group('beneficios');
$router->get('/novo', 'BeneficioController:create');
$router->get('/editar/{id}', 'BeneficioController:edit');
$router->post('/insert', 'BeneficioController:insert');
$router->post('/update/{id}', 'BeneficioController:update');
$router->get('/delete/{id}', 'BeneficioController:delete');

/*
 * Error
 */
$router->group('error');
$router->get('/{errcode}', 'ErrorController:notFound');

$router->dispatch();

/*
 * Redirecionar todos os erros
 */
if ($router->error()) {
	$router->redirect("/error/{$router->error()}");
}