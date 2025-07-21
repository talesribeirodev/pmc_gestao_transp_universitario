<?php
namespace App\Controllers;

use App\Core\HttpClient;

class LoginController {

    public function index() {
        if (isset($_SESSION['usuario'])) {
            header('Location: ' . BASE_URL . '?url=home/index');
            exit;
        }

        $erro = $_SESSION['erro'] ?? null;
        unset($_SESSION['erro']);

        require_once __DIR__ . '/../Views/login/index.php';
    }

    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cpf   = $_POST['cpf'] ?? '';
            $senha = $_POST['senha'] ?? '';

            $client = new HttpClient();
            $response = $client->post('/acessosintranet/login', [
                'UserName' => $cpf,
                'Password' => $senha
            ]);

            if (isset($response['Status'])) {
                if ($response['Status'] === 200) {
                    $_SESSION['usuario'] = $response['Details'];
                    header('Location: ' . BASE_URL . '?url=home/index');
                    exit;
                }

                if ($response['Status'] === 401 || $response['Status'] === 400) {
                    $_SESSION['erro'] = 'Credenciais inválidas.';
                } else {
                    $_SESSION['erro'] = $response['Error'] ?? 'Erro desconhecido.';
                }
            } else {
                $_SESSION['erro'] = 'Resposta inválida da API.';
            }

            header('Location: ' . BASE_URL . '?url=login/index');
            exit;
        }
    }

    public function sair() {
        session_destroy();
        header('Location: ' . BASE_URL . '?url=login/index');
        exit;
    }
}
