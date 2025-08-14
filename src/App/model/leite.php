<?php

namespace App\model;

use PDO;

class Leite
{
    private $pdo;

    
    public function __construct($db)
    {
        $this->pdo = $db;
    }

    
    public function create(
        $data_producao,
        $quantidade_litros,
        $responsavel,
        $turno = null,
        $tipo_leite = null,
        $qualidade = null,
        $temperatura = null,
        $equipamento_utilizado = null,
        $animais_contribuintes = null, // deve vir como string do tipo '{"1","2"}'
        $local_armazenamento = null,
        $observacao = null
    ) {
        $sql = "INSERT INTO leite (
            data_producao,
            quantidade_litros,
            responsavel,
            turno,
            tipo_leite,
            qualidade,
            temperatura,
            equipamento_utilizado,
            animais_contribuintes,
            local_armazenamento,
            observacao
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data_producao,
            $quantidade_litros,
            $responsavel,
            $turno,
            $tipo_leite,
            $qualidade,
            $temperatura,
            $equipamento_utilizado,
            $animais_contribuintes,
            $local_armazenamento,
            $observacao
        ]);
    }

     public function update(
        $id,
        $data_producao,
        $quantidade_litros,
        $responsavel,
        $turno = null,
        $tipo_leite = null,
        $qualidade = null,
        $temperatura = null,
        $equipamento_utilizado = null,
        $animais_contribuintes = null,
        $local_armazenamento = null,
        $observacao = null
    ) {
        $sql = "UPDATE leite SET 
            data_producao = ?,
            quantidade_litros = ?,
            responsavel = ?,
            turno = ?,
            tipo_leite = ?,
            qualidade = ?,
            temperatura = ?,
            equipamento_utilizado = ?,
            animais_contribuintes = ?,
            local_armazenamento = ?,
            observacao = ?
        WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $data_producao,
            $quantidade_litros,
            $responsavel,
            $turno,
            $tipo_leite,
            $qualidade,
            $temperatura,
            $equipamento_utilizado,
            $animais_contribuintes,
            $local_armazenamento,
            $observacao,
            $id
        ]);
    }

     public function getAllLeites()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM leite");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
{
    $stmt = $this->pdo->prepare("SELECT * FROM leite WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM leite WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function somarLeite($data_inicio = null, $data_fim = null)
    {
        $sql = "SELECT quantidade_litros FROM leite";

        if ($data_inicio && $data_fim) {
            $sql .= " WHERE data_producao BETWEEN ? AND ?";
        }

        $stmt = $this->pdo->prepare($sql);

        if ($data_inicio && $data_fim) {
            $stmt->execute([$data_inicio, $data_fim]);
        } else {
            $stmt->execute();
        }

        $totalLitros = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $totalLitros += $row['quantidade_litros'];
        }

        return $totalLitros;
    }
}

