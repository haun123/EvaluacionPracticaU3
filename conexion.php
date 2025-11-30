<?php
class Database {
    private $host = "localhost";
    private $db = "dispensario_medico";
    private $user = "root"; 
    private $pass = "";    
    private $charset = "utf8mb4";

    public function conectar() {
        try {
            $pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}",
                $this->user,
                $this->pass
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;

        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
