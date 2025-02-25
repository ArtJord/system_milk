<?php

require_once __DIR__ . '/../model/Vaca.php';

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
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->numero)) {
            http_response_code(400);
            echo json_encode(["message" => "O número é obrigatório."]);
            return;
        }

        try {
            $resultado = $this->vaca->create(
                $data->numero,
                isset($data->nome) ? $data->nome : null,
                isset($data->raca) ? $data->raca : null,
                isset($data->descricao) ? $data->descricao : null
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Vaca cadastrada com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao cadastrar vaca."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
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
