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

     public function create(
        $data_receita = null,
        $categoria = null,
        $fonte_receita = null,
        $cliente = null,
        $descricao = null,
        $quantidade = null,
        $preco_unitario = null,
        $valor_total = null,
        $nota_fiscal = null,
        $metodo_pagamento = null,
        $status_pagamento = null,
        $data_vencimento = null,
        $data_pagamento = null,
        $observacoes = null
    ) {
        $sql = "INSERT INTO lucro (
            data_receita,
            categoria,
            fonte_receita,
            cliente,
            descricao,
            quantidade,
            preco_unitario,
            valor_total,
            nota_fiscal,
            metodo_pagamento,
            status_pagamento,
            data_vencimento,
            data_pagamento,
            observacoes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data_receita,
            $categoria,
            $fonte_receita,
            $cliente,
            $descricao,
            $quantidade,
            $preco_unitario,
            $valor_total,
            $nota_fiscal,
            $metodo_pagamento,
            $status_pagamento,
            $data_vencimento,
            $data_pagamento,
            $observacoes
        ]);
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
    $valor_total = null,
    $nota_fiscal = null,
    $metodo_pagamento = null,
    $status_pagamento = null,
    $data_vencimento = null,
    $data_pagamento = null,
    $observacoes = null
) {
    $sql = "UPDATE lucro SET
        data_receita = ?,
        categoria = ?,
        fonte_receita = ?,
        cliente = ?,
        descricao = ?,
        quantidade = ?,
        preco_unitario = ?,
        valor_total = ?,
        nota_fiscal = ?,
        metodo_pagamento = ?,
        status_pagamento = ?,
        data_vencimento = ?,
        data_pagamento = ?,
        observacoes = ?
    WHERE id = ?";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $data_receita,
        $categoria,
        $fonte_receita,
        $cliente,
        $descricao,
        $quantidade,
        $preco_unitario,
        $valor_total,
        $nota_fiscal,
        $metodo_pagamento,
        $status_pagamento,
        $data_vencimento,
        $data_pagamento,
        $observacoes,
        $id
    ]);
}

    public function getById($id)
{
    $stmt = $this->pdo->prepare("SELECT * FROM lucro WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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
   


