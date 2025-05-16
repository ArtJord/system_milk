<?php

namespace App\model;

use PDO;
use Exception;

class Despesa
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function create($nomeDespesa, $valor, $data, $nota_fiscal = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO despesa (nome_despesa, valor, data, nota_fiscal) 
                                     VALUES (?, ?, ?, ?)");

        return $stmt->execute([
            $nomeDespesa,
            $valor,
            $data,
            $nota_fiscal
        ]);
    }

    public function update($id, $nomeDespesa, $valor, $data, $nota_fiscal = null)
    {
        $stmt = $this->pdo->prepare("UPDATE despesa SET nome_despesa = ?, valor = ?, data = ?, nota_fiscal = ? WHERE id = ?");

        return $stmt->execute([
            $nomeDespesa,
            $valor,
            $data,
            $nota_fiscal,
            $id
        ]);
    }

    public function getAllDespesas()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM despesa");
        $stmt->execute();

        $despesas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $despesas[] = $row;
        }

        return $despesas;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM despesa WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
