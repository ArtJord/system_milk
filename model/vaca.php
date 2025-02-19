<?php

class Vaca {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($numero, $nome = null, $raca = null, $descricao = null) {
        try {
            $query = "INSERT INTO vaca (numero, nome, raca, descricao) 
                      VALUES (:numero, :nome, :raca, :descricao)";
            $stmt = $this->conn->prepare($query);

            
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':raca', $raca);
            $stmt->bindParam(':descricao', $descricao);

           
            return $stmt->execute();
        } catch (PDOException $e) {
            
            throw new Exception("Erro na consulta: " . $e->getMessage());
        }
    }
}
