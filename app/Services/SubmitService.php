<?php
namespace App\Services;

require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Core/Env.php';

use App\Models\Database;
use App\Core\Env;
use PDO;

class SubmitService
{
    private $db;
    private $cpf_user;

    public function __construct()
    {
        $this->db = (new Database())->connect();
        $this->cpf_user = $_SESSION['usuario']['SamAccountName'] ?? '-';
    }

    public function listarInscricoesRead()
    {
        $sql = "SELECT * FROM inscricoes_read ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarInscricoesWrite()
    {
        $sql = "SELECT i.*, s.nome AS nome_status 
                FROM inscricoes_write i
                LEFT JOIN status s ON i.id_status = s.id
                ORDER BY i.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $cpfsBloqueadosStr = Env::get('CPFS_BLOQUEADOS');
        $cpfsBloqueados = array_map('trim', explode(',', $cpfsBloqueadosStr));

        // Filtra as inscrições removendo as que têm CPF bloqueado
        $inscricoes = array_filter($inscricoes, function ($inscricao) use ($cpfsBloqueados) {
            return !in_array($inscricao['cpf'], $cpfsBloqueados);
        });

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https" : "http");
        $host = $_SERVER['HTTP_HOST'];
        $urlPhotos = Env::get('MAIN_PROJECT_URL');
        $basePath = "$protocol://$host/$urlPhotos/app/uploads";

        foreach ($inscricoes as &$inscricao) {
            $cpf = $inscricao['cpf'];

            $inscricao['caminhos_imagens'] = [];

            if (!empty($inscricao['nome_doc_residencia'])) {
                $inscricao['caminhos_imagens'][] = "$basePath/$cpf/{$inscricao['nome_doc_residencia']}";
            }

            if (!empty($inscricao['nome_doc_matricula'])) {
                $inscricao['caminhos_imagens'][] = "$basePath/$cpf/{$inscricao['nome_doc_matricula']}";
            }

            if (!empty($inscricao['nome_doc_eleitor'])) {
                $inscricao['caminhos_imagens'][] = "$basePath/$cpf/{$inscricao['nome_doc_eleitor']}";
            }

            if (!empty($inscricao['nome_foto'])) {
                $inscricao['caminhos_imagens'][] = "$basePath/$cpf/{$inscricao['nome_foto']}";
            }
        }
        unset($inscricao);

        return array_values($inscricoes);
    }

    public function aprovarInscricao($id)
    {
        $sql = "UPDATE inscricoes_write 
            SET id_status = 2, 
                cpf_user = :cpf_user, 
                updatedAt = CURRENT_TIMESTAMP 
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':cpf_user', $this->cpf_user, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function cancelarInscricao($id, $justificativa)
    {
        $sql = "UPDATE inscricoes_write 
            SET id_status = 3, 
                justificativa = :justificativa, 
                cpf_user = :cpf_user, 
                updatedAt = CURRENT_TIMESTAMP 
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':justificativa', $justificativa, PDO::PARAM_STR);
        $stmt->bindParam(':cpf_user', $this->cpf_user, PDO::PARAM_STR);
        return $stmt->execute();
    }


    public function buscarInscricaoReadPorCpf($cpf)
    {
        $sql = "SELECT * FROM inscricoes_read WHERE cpf = :cpf LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':cpf', $cpf);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
