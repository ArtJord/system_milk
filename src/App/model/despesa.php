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
        $numero_nfe = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        try {
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
            numero_nfe,
            data_vencimento,
            data_pagamento,
            observacoes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $numero_despesa,
                $data_despesa,
                $prioridade,
                $categoria,
                $subcategoria,
                $descricao,
                $fornecedor,
                $quantidade,
                $preco_unitario,
                $numero_nfe,
                $data_vencimento,
                $data_pagamento,
                $observacoes
            ]);

            $id = $stmt->fetchColumn();
            return $this->getById($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao criar despesa: " . $e->getMessage());
        }
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
        $numero_nfe = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        try {
            $sql = "UPDATE despesa SET
            numero_despesa = ?,
            data_despesa = ?,
            prioridade    = ?,
            categoria     = ?,
            subcategoria  = ?,
            descricao     = ?,
            fornecedor    = ?,
            quantidade    = ?,
            preco_unitario= ?,
            numero_nfe    = ?,
            data_vencimento = ?,
            data_pagamento  = ?,
            observacoes     = ?
        WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $numero_despesa,
                $data_despesa,
                $prioridade,
                $categoria,
                $subcategoria,
                $descricao,
                $fornecedor,
                $quantidade,
                $preco_unitario,
                $numero_nfe,
                $data_vencimento,
                $data_pagamento,
                $observacoes,
                $id
            ]);

            return $this->getById($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar despesa: " . $e->getMessage());
        }
    }

    public function getAllDespesas($inicio = null, $fim = null, $categoria = null, $prioridade = null)
    {
        $sql = "SELECT * FROM despesa WHERE 1=1";
        $params = [];

        if ($inicio) {
            $sql .= " AND data_despesa >= ?";
            $params[] = $inicio;
        }
        if ($fim) {
            $sql .= " AND data_despesa <= ?";
            $params[] = $fim;
        }
        if ($categoria) {
            $sql .= " AND categoria = ?";
            $params[] = $categoria;
        }
        if ($prioridade) {
            $sql .= " AND prioridade = ?";
            $params[] = $prioridade;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
