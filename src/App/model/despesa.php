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

     public function create(
        $numero_despesa = null,
        $data_despesa = null,
        $prioridade = null,
        $categoria = null,
        $subcategoria = null,
        $descricao = null,
        $fornecedor = null,
        $quantidade = null,
        $preco_unitario = null,
        $valor_total = null,
        $numero_nfe = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        $sql = "INSERT INTO despesa (
            numero_despesa,
            data_despesa,
            prioridade,
            categoria,
            subcategoria,
            descricao,
            fornecedor,
            quantidade,
            preco_unitario,
            valor_total,
            numero_nfe,
            data_vencimento,
            data_pagamento,
            observacoes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $numero_despesa,
            $data_despesa,
            $prioridade,
            $categoria,
            $subcategoria,
            $descricao,
            $fornecedor,
            $quantidade,
            $preco_unitario,
            $valor_total,
            $numero_nfe,
            $data_vencimento,
            $data_pagamento,
            $observacoes
        ]);
    }

    public function update(
        $id,
        $numero_despesa = null,
        $data_despesa = null,
        $prioridade = null,
        $categoria = null,
        $subcategoria = null,
        $descricao = null,
        $fornecedor = null,
        $quantidade = null,
        $preco_unitario = null,
        $valor_total = null,
        $numero_nfe = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        $sql = "UPDATE despesa SET 
            numero_despesa = ?, 
            data_despesa = ?, 
            prioridade = ?, 
            categoria = ?, 
            subcategoria = ?, 
            descricao = ?, 
            fornecedor = ?, 
            quantidade = ?, 
            preco_unitario = ?, 
            valor_total = ?, 
            numero_nfe = ?, 
            data_vencimento = ?, 
            data_pagamento = ?, 
            observacoes = ?
        WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $numero_despesa,
            $data_despesa,
            $prioridade,
            $categoria,
            $subcategoria,
            $descricao,
            $fornecedor,
            $quantidade,
            $preco_unitario,
            $valor_total,
            $numero_nfe,
            $data_vencimento,
            $data_pagamento,
            $observacoes,
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

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM despesa WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM despesa WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

