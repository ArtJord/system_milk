<?php

namespace App\model;

use PDO;
use Exception;


class Lucro
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function create($origem, $quantidade, $valor, $data, $tipo = null, $nota_fiscal = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO lucro (origem, quantidade, valor, data, tipo, nota_fiscal) 
                                     VALUES (?, ?, ?, ?, ?, ?)");

        return $stmt->execute([
            $origem,
            $quantidade,
            $valor,
            $data,
            $tipo,
            $nota_fiscal
        ]);
    }

    public function update($id, $origem, $quantidade, $valor, $data, $tipo = null, $nota_fiscal = null)
    {
        $stmt = $this->pdo->prepare("UPDATE lucro SET origem = ?, quantidade = ?, valor = ?, data = ?, tipo = ?, nota_fiscal = ? WHERE id = ?");

        return $stmt->execute([
            $origem,
            $quantidade,
            $valor,
            $data,
            $tipo,
            $nota_fiscal,
            $id
        ]);
    }

    public function getAllLucros()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lucro");
        $stmt->execute();

        $lucros = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lucros[] = $row;
        }

        return $lucros;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM lucro WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
   


