<?php

namespace App\controllers;

use App\model\Leite;
use PDO;
use Exception;

class LeiteController
{
    private $leite;
    private $user_cargo;

   
    public function __construct($db, $user_cargo)
    {
        $this->leite = new Leite($db);
        $this->user_cargo = $user_cargo;
    }

    public function setUserCargo($cargo) { $this->user_cargo = $cargo; }


    
   public function create(){

    header('Content-Type: application/json; charset=utf-8');

    $ct = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    $raw = file_get_contents('php://input');

    if (stripos($ct, 'application/json') !== false) {
        $data = json_decode($raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                "message" => "JSON inválido",
                "error"   => json_last_error_msg()
            ]);
            exit;
        }
    } else {
        
        $data = (object) $_POST;
    }

    if (!isset($data->quantidade_litros) || !isset($data->data_producao) || !isset($data->responsavel)) {
        http_response_code(400);
        echo json_encode(["message" => "A data de produção, a quantidade de litros e o responsável são obrigatórios."]);
        exit;
    }

    try {
        $resultado = $this->leite->create(
            $data->data_producao,
            $data->quantidade_litros,
            $data->responsavel,
            $data->turno              ?? null,
            $data->tipo_leite         ?? null,
            $data->qualidade          ?? null,
            $data->temperatura        ?? null,
            $data->equipamento_utilizado ?? null,
            $data->animais_contribuintes ?? null,
            $data->local_armazenamento   ?? null,
            $data->observacao            ?? null
        );

        if ($resultado) {
            http_response_code(201);
            echo json_encode(["message" => "Leite registrado com sucesso."]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao registrar o leite."]);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        exit;
    }
}

   public function update($id)
{
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || !isset($data->quantidade_litros) || !isset($data->data_producao) || !isset($data->responsavel)) {
        http_response_code(400);
        echo json_encode(["message" => "ID, data de produção, quantidade e responsável são obrigatórios."]);
        return;
    }

    try {
        $resultado = $this->leite->update(
            $data->id,
            $data->data_producao,
            $data->quantidade_litros,
            $data->responsavel,
            isset($data->turno) ? $data->turno : null,
            isset($data->tipo_leite) ? $data->tipo_leite : null,
            isset($data->qualidade) ? $data->qualidade : null,
            isset($data->temperatura) ? $data->temperatura : null,
            isset($data->equipamento_utilizado) ? $data->equipamento_utilizado : null,
            isset($data->animais_contribuintes) ? $data->animais_contribuintes : null,
            isset($data->local_armazenamento) ? $data->local_armazenamento : null,
            isset($data->observacao) ? $data->observacao : null
        );

        if ($resultado) {
            http_response_code(200);
            echo json_encode(["message" => "Leite atualizado com sucesso."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar o leite."]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Erro: " . $e->getMessage()]);
    }
}


    public function getAllLeites()
    {
        try {
            $leites = $this->leite->getAllLeites();
            http_response_code(200);
            echo json_encode(["leites" => $leites]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function getById($id)
{
    try {
        $leite = $this->leite->getById($id);

        if ($leite) {
            http_response_code(200);
            echo json_encode(["leite" => $leite]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Registro de leite não encontrado."]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Erro: " . $e->getMessage()]);
    }
}



    
    public function delete($id)
    {

        if($this->user_cargo != 'gerente'){
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir"]);
        }

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id)) {
            http_response_code(400);
            echo json_encode(["message" => "O ID é obrigatório para excluir o leite."]);
            return;
        }

        try {
            $resultado = $this->leite->delete($data->id);

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Leite excluído com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir o leite."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    
    public function somarLeite()
{
    try {
        // Obtém as datas do corpo da requisição
        $data = json_decode(file_get_contents("php://input"));

        // Se as datas forem fornecidas, passa para o modelo, senão, considera o intervalo completo
        $data_inicio = isset($data->data_inicio) ? $data->data_inicio : null;
        $data_fim = isset($data->data_fim) ? $data->data_fim : null;

        // Chama o método para somar a quantidade de leite no intervalo de datas
        $totalLeite = $this->leite->somarLeite($data_inicio, $data_fim);

        // Retorna o total de leite somado
        http_response_code(200);
        echo json_encode(["total_leite" => $totalLeite]);
    } catch (Exception $e) {
        // Caso haja um erro durante a soma
        http_response_code(500);
        echo json_encode(["message" => "Erro: " . $e->getMessage()]);
    }
}

}

