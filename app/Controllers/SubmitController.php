<?php
namespace App\Controllers;

require_once __DIR__ . '/../Services/SubmitService.php';
use App\Services\SubmitService;

class SubmitController
{
    public function listarRead()
    {
        $service = new SubmitService();
        $dados = $service->listarInscricoesRead();
        require_once __DIR__ . '/../Views/inscricoes/listar_read.php';
    }

    public function listarWrite()
    {
        $service = new SubmitService();
        $dados = $service->listarInscricoesWrite();
        require_once __DIR__ . '/../Views/inscricoes/listar_write.php';
    }

    public function aprovar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if ($id) {
                $service = new SubmitService();
                $success = $service->aprovarInscricao($id);

                header('Content-Type: application/json');
                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Falha ao aprovar inscrição']);
                }
                exit;
            }

            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID não informado']);
            exit;
        }
    }

    public function cancelar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        $justificativa = trim($_POST['justificativa'] ?? '');

        if (!$id || $justificativa === '') {
            http_response_code(400);
            echo json_encode(['error' => 'ID e justificativa são obrigatórios']);
            exit;
        }

        $service = new SubmitService();

        $sucesso = $service->cancelarInscricao($id, $justificativa);

        if ($sucesso) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Falha ao cancelar inscrição']);
        }
        exit;
    }

    public function dadosReadPorCpf()
    {
        $cpf = $_GET['cpf'] ?? null;
        if (!$cpf) {
            http_response_code(400);
            echo json_encode(['error' => 'CPF não informado']);
            exit;
        }

        $service = new SubmitService();
        $dadosRead = $service->buscarInscricaoReadPorCpf($cpf);

        header('Content-Type: application/json');
        echo json_encode($dadosRead);
    }

}
