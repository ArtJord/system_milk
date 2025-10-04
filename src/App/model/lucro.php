<?php

namespace App\model;

use PDO;
use Exception;


class Lucro
{
    private $pdo;
    private $table = 'lucro';

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function create(
        $data_receita = null,
        $categoria = null,
        $fonte_receita = null,
        $cliente = null,
        $descricao = null,
        $quantidade = null,
        $preco_unitario = null,
        $numero_nfe = null,
        $metodo_pagamento = null,
        $status_pagamento = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        try {
            $sql = "INSERT INTO {$this->table} (
        data_receita,
        categoria,
        fonte_receita,
        cliente,
        descricao,
        quantidade,
        preco_unitario,
        numero_nfe,
        metodo_pagamento,
        status_pagamento,
        data_vencimento,
        data_pagamento,
        observacoes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    RETURNING id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data_receita,
                $categoria,
                $fonte_receita,
                $cliente,
                $descricao,
                $quantidade,
                $preco_unitario,
                $numero_nfe,
                $metodo_pagamento,
                $status_pagamento,
                $data_vencimento,
                $data_pagamento,
                $observacoes
            ]);

            $id = $stmt->fetchColumn();
            return $this->getById($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao criar Lucro: " . $e->getMessage());
        }
    }


    public function update(
        $id,
        $data_receita = null,
        $categoria = null,
        $fonte_receita = null,
        $cliente = null,
        $descricao = null,
        $quantidade = null,
        $preco_unitario = null,
        $numero_nfe = null,
        $metodo_pagamento = null,
        $status_pagamento = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        try {
            $sql = "UPDATE {$this->table} SET
    data_receita = ?,
    categoria = ?,
    fonte_receita = ?,
    cliente = ?,
    descricao = ?,
    quantidade = ?,
    preco_unitario = ?,
    numero_nfe = ?,
    metodo_pagamento = ?,
    status_pagamento = ?,
    data_vencimento = ?,
    data_pagamento = ?,
    observacoes = ?
    WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data_receita,
                $categoria,
                $fonte_receita,
                $cliente,
                $descricao,
                $quantidade,
                $preco_unitario,
                $numero_nfe,
                $metodo_pagamento,
                $status_pagamento,
                $data_vencimento,
                $data_pagamento,
                $observacoes,
                $id
            ]);

            return $this->getById($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar Lucro: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAllLucros($inicio = null, $fim = null, $categoria = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($inicio) {
            $sql .= " AND data_receita >= ?";
            $params[] = $inicio;
        }
        if ($fim) {
            $sql .= " AND data_receita <= ?";
            $params[] = $fim;
        }
        if ($categoria) {
            $sql .= " AND categoria = ?";
            $params[] = $categoria;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
