<?php
namespace App\Controllers;

require_once __DIR__ . '/../Services/SubmitService.php';
use App\Services\SubmitService;

class HomeController {

    public function index() {
        if (!isset($_SESSION['usuario'])) {
            $erro = $_SESSION['erro'] ?? null;
            unset($_SESSION['erro']);
            require_once __DIR__ . '/../Views/login/index.php';
            return;
        }

        $service = new SubmitService();
        $dados = $service->listarInscricoesWrite();
            
        foreach ($dados as &$inscricao) {
            $cpf = $inscricao['cpf'];
            $inscricao['read'] = $service->buscarInscricaoReadPorCpf($cpf);
        }
        unset($inscricao);

        require_once __DIR__ . '/../Views/home/index.php';
    }
}
