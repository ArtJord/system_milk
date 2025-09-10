<?php

namespace App\Model;

use PDO;

class Vaca
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function create(
        $numero_animal,
        $nome_animal = null,
        $raca = null,
        $sexo = null,
        $data_nascimento = null,
        $peso_kg = null,
        $cor = null,
        $statuss = null,
        $estado_saude = null,
        $ultima_vacinacao = null,
        $proxima_vacinacao = null,
        $status_reprodutivo = null,
        $producao_diaria_litros = null,
        $foto = null,
        $observacoes = null,
        $criado_em = null
    ) {
        $sql = "INSERT INTO animais (
            numero_animal,
            nome_animal,
            raca,
            sexo,
            data_nascimento,
            peso_kg,
            cor,
            statuss,
            estado_saude,
            ultima_vacinacao,
            proxima_vacinacao,
            status_reprodutivo,
            producao_diaria_litros,
            foto,
            observacoes,
            criado_em
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $numero_animal,
            $nome_animal,
            $raca,
            $sexo,
            $data_nascimento,
            $peso_kg,
            $cor,
            $statuss,
            $estado_saude,
            $ultima_vacinacao,
            $proxima_vacinacao,
            $status_reprodutivo,
            $producao_diaria_litros,
            $foto,
            $observacoes,
            $criado_em
        ]);
    }

    public function update($id, $numero_animal, $nome_animal = null, $raca = null, $observacoes = null)
    {
        $stmt = $this->pdo->prepare("UPDATE animais SET numero_animal = ?, nome_animal = ?, raca = ?, observacoes = ? WHERE id = ?");
        return $stmt->execute([$numero_animal, $nome_animal, $raca, $observacoes, $id]);
    }

    public function updateParcial(int $id, array $dados): bool
{
    // Lista branca de colunas válidas
    $permitidos = [
        'numero_animal',
        'nome_animal',
        'raca',
        'sexo',
        'data_nascimento',
        'peso_kg',
        'cor',
        'statuss',
        'estado_saude',
        'ultima_vacinacao',
        'proxima_vacinacao',
        'status_reprodutivo',
        'producao_diaria_litros',
        'observacoes',
        
    ];

    $sets = [];
    $params = [':id' => $id];

    foreach ($dados as $col => $val) {
        if (!in_array($col, $permitidos, true)) continue;
        $sets[] = "{$col} = :{$col}";
        $params[":{$col}"] = $val;
    }

    if (empty($sets)) {
        
        return true;
    }

    $sql = "UPDATE animais SET " . implode(', ', $sets) . " WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}


    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM animais WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM animais");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $query = "SELECT * FROM animais WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
