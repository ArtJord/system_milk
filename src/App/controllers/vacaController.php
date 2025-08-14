<?php

namespace App\Controllers;

use App\Model\Vaca; 
use PDO;
use Exception;

class VacaController
{
    private $vaca;

    public function __construct($db)
    {
        $this->vaca = new Vaca($db);
    }

    // Método para criar uma nova vaca
 public function create()
{
    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->numero_animal) || empty($data->numero_animal)) {
        http_response_code(400);
        echo json_encode(["message" => "O número é obrigatório."]);
        exit;
    }

    try {
        $resultado = $this->vaca->create(
            $data->numero_animal,
            $data->nome_animal ?? null,
            $data->raca ?? null,
            $data->sexo ?? null, // lembre-se: no banco é CHAR(1): 'M' ou 'F'
            $data->data_nascimento ?? null,
            $data->peso_kg ?? null,
            $data->cor ?? null,
            $data->statuss ?? null,
            $data->estado_saude ?? null,
            $data->ultima_vacinacao ?? null,
            $data->proxima_vacinacao ?? null,
            $data->status_reprodutivo ?? null,
            $data->producao_diaria_litros ?? null,
            null,
            $data->observacoes ?? null,
            $data->criado_em ?? date("Y-m-d H:i:s")
        );

        if ($resultado) {
            http_response_code(201); // Created (opcional trocar para 200)
            echo json_encode(["message" => "Vaca cadastrada com sucesso."]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao cadastrar vaca."]);
            exit;
        }

    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        exit;
    }
}


    // Método para atualizar uma vaca
    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->numero)) {
            http_response_code(400);
            echo json_encode(["message" => "O número é obrigatório para atualizar a vaca."]);
            return;
        }

        try {
            $resultado = $this->vaca->update(
                $id,
                $data->numero,
                isset($data->nome) ? $data->nome : null,
                isset($data->raca) ? $data->raca : null,
                isset($data->descricao) ? $data->descricao : null
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Vaca atualizada com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao atualizar vaca."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar vaca: " . $e->getMessage()]);
        }
    }

    // Método para deletar uma vaca
    public function delete($id)
    {
        try {
            $resultado = $this->vaca->delete($id);

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Vaca deletada com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao deletar vaca."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao deletar vaca: " . $e->getMessage()]);
        }
    }

    // Método para buscar todas as vacas
    public function findAll()
    {
        $vacas = $this->vaca->findAll();

        if ($vacas) {
            http_response_code(200);
            echo json_encode($vacas);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Nenhuma vaca encontrada."]);
        }
    }

    // Método para buscar uma vaca por ID
    public function findById($id)
{
    
    $id = (int) $id;

    try {
        
        $vaca = $this->vaca->findById($id);

        if ($vaca) {
            
            echo json_encode($vaca);
        } else {
            
            http_response_code(404);
            echo json_encode(["message" => "Vaca não encontrada"]);
        }
    } catch (Exception $e) {
        
        http_response_code(500);
        echo json_encode(["message" => "Erro ao buscar vaca: " . $e->getMessage()]);
    }
}


}
