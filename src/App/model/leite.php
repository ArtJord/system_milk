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

        $leites = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leites[] = $row;
        }

        return $leites;
    }

    
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM leite WHERE id = ?");
        return $stmt->execute([$id]);
    }

    
    public function somarLeite($data_inicio = null, $data_fim = null)
{
    // Inicia a consulta SQL
    $sql = "SELECT quantidade_litros, unidade_quantidade FROM leite";

    // Se foi passado um intervalo de datas, adicionamos isso à consulta
    if ($data_inicio && $data_fim) {
        $sql .= " WHERE data_fabricacao BETWEEN ? AND ?";
    }

    // Prepara a declaração
    $stmt = $this->pdo->prepare($sql);

    // Se houver intervalo de datas, passamos as datas para a execução da query
    if ($data_inicio && $data_fim) {
        $stmt->execute([$data_inicio, $data_fim]);
    } else {
        // Caso não haja intervalo de datas, executamos a query sem filtros
        $stmt->execute();
    }

    // Variável para somar os litros
    $totalLitros = 0;

    // Processa as linhas retornadas
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Se a unidade for 'ml', converte para litros
        if ($row['unidade_quantidade'] == 'ml') {
            $quantidadeEmLitros = $row['quantidade_litros'] / 1000;
        } else {
            $quantidadeEmLitros = $row['quantidade_litros'];
        }

        // Soma a quantidade convertida ao total
        $totalLitros += $quantidadeEmLitros;
    }

    return $totalLitros;
}
}
?>
