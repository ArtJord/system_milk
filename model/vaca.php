<?php

class Vaca {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($numero, $nome = null, $raca = null, $racao = null, $remedio = null, $bezerro = false, $descricao = null) {
        try {
            $query = "INSERT INTO vaca (numero, nome, raca, racao, remedio, bezerro, descricao) 
                      VALUES (:numero, :nome, :raca, :racao, :remedio, :bezerro, :descricao)";
            $stmt = $this->conn->prepare($query);

            
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':raca', $raca);
            $stmt->bindParam(':racao', $racao);
            $stmt->bindParam(':remedio', $remedio);
            $stmt->bindParam(':bezerro', $bezerro);
            $stmt->bindParam(':descricao', $descricao);

           
            return $stmt->execute();
        } catch (PDOException $e) {
            
            throw new Exception("Erro na consulta: " . $e->getMessage());
        }
    }
}
