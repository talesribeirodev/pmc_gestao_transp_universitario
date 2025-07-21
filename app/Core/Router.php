<?php
namespace App\Core;

class Router
{
    public function dispatch()
    {
        $url = $_GET['url'] ?? 'home/index';
        $url = trim($url, '/');

        $parts = explode('/', $url);

        $controllerName = ucfirst($parts[0] ?? 'Home') . 'Controller';
        $method         = $parts[1] ?? 'index';

        $rotaLivre = ($controllerName === 'LoginController' && ($method === 'index' || $method === 'autenticar'));

        if (!isset($_SESSION['usuario']) && !$rotaLivre) {
            header('Location: ' . BASE_URL . '?url=login/index');
            exit;
        }

        $controllerPath = __DIR__ . '/../Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerPath)) {
            $this->notFound("Controller '$controllerName' não encontrado.");
            return;
        }

        require_once $controllerPath;

        $controllerClass = "App\\Controllers\\$controllerName";

        if (!class_exists($controllerClass)) {
            $this->notFound("Classe '$controllerClass' não encontrada.");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            $this->notFound("Método '$method' não encontrado no controller '$controllerName'.");
            return;
        }

        call_user_func([$controller, $method]);
    }

    private function notFound($msg = 'Página não encontrada.')
    {
        http_response_code(404);
        echo "<h1>404</h1><p>$msg</p>";
    }
}
