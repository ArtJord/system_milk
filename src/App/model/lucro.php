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
        $numero_nfe = null,
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
            numero_nfe,
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
            $numero_nfe,
            $metodo_pagamento,
            $status_pagamento,
            $data_vencimento,
            $data_pagamento,
            $observacoes
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
   


