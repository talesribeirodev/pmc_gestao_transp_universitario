<?php
session_start();

define('BASE_URL', '/transporte_universitario/public/'); // Alterar para o nome do seu domínio

require_once __DIR__ . '/../app/Core/Env.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/HttpClient.php';

use App\Core\Env;
use App\Core\Router;

Env::load();

$router = new Router();
$router->dispatch();
