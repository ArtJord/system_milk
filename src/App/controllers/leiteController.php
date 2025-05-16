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

    
    public function create()
    {
        // Obtém os dados da requisição (body JSON)
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->quantidade) || !isset($data->data_fabricacao)) {
            http_response_code(400);
            echo json_encode(["message" => "A quantidade e a data de fabricação são obrigatórias."]);
            return;
        }

        $criado_por = isset($data->criado_por) ? $data->criado_por : null;
        $unidade_quantidade = isset($data->unidade_quantidade) ? $data->unidade_quantidade : 'litros';
        $tipo_leite = isset($data->tipo_leite) ? $data->tipo_leite : null;

        try {
            $resultado = $this->leite->create(
                $data->data_fabricacao,
                $data->quantidade,
                $unidade_quantidade, $tipo_leite, $criado_por
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Leite registrado com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao registrar o leite."]);
            }
        } catch (Exception $e) {
    
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function update()
    {

        
        $data = json_decode(file_get_contents("php://input"));

        
        if (!isset($data->id) || !isset($data->quantidade) || !isset($data->data_fabricacao)) {
            http_response_code(400);
            echo json_encode(["message" => "O ID, quantidade e data de fabricação são obrigatórios."]);
            return;
        }

        try {
            
            $resultado = $this->leite->update(
                $data->id,
                $data->data_fabricacao,
                $data->quantidade,
                isset($data->unidade_quantidade) ? $data->unidade_quantidade : 'litros'
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

    
    public function delete()
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

