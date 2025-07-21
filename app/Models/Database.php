<?php
namespace App\Models;

use PDO;
use PDOException;

class Database
{
    private $host = "localhost";
    private $db_name = "cajamarspgov_transporteuniv";
    private $username = "root";
    private $password = "";
    private $conn;

    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na conexão com o banco: " . $e->getMessage());
        }

        return $this->conn;
    }
}