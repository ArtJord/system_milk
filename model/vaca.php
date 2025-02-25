<?php

class Vaca
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function create($numero, $nome = null, $raca = null, $descricao = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO vaca (numero, nome, raca, descricao) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$numero, $nome, $raca, $descricao]);
    }

    public function update($id, $numero, $nome = null, $raca = null, $descricao = null)
    {
        $stmt = $this->pdo->prepare("UPDATE vaca SET numero = ?, nome = ?, raca = ?, descricao = ? WHERE id = ?");
        return $stmt->execute([$numero, $nome, $raca, $descricao, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM vaca WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM vaca");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        
        $query = "SELECT * FROM vaca WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna a vaca ou null se não encontrar
    }
}
