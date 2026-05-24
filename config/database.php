<?php 

class Database {

    private $host = 'localhost';
    private $db_name = 'drug_4_u';
    private $user = 'root';
    private $pass = '';

    private function connect() {

        try {

            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->user,
                $this->pass
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (Exception $e) {

            die(json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]));

        }

    }

}

?>