<?php

namespace App\controllers;

use App\model\Despesa;
use PDO;
use Exception;

class DespesaController
{
    private $despesa;
    private $user_cargo;

    public function __construct($db, $user_cargo)
    {
        $this->despesa = new Despesa($db);
        $this->user_cargo = $user_cargo;
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome_despesa) || !isset($data->valor) || !isset($data->data)) {
            http_response_code(400);
            echo json_encode(["message" => "Nome da despesa, valor e data são obrigatórios."]);
            return;
        }

        $nota_fiscal = isset($data->nota_fiscal) ? $data->nota_fiscal : null;

        try {
            $resultado = $this->despesa->create(
                $data->nome_despesa,
                $data->valor,
                $data->data,
                $nota_fiscal
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Despesa registrada com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao registrar a despesa."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function update()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id) || !isset($data->nome_despesa) || !isset($data->valor) || !isset($data->data)) {
            http_response_code(400);
            echo json_encode(["message" => "ID, nome da despesa, valor e data são obrigatórios."]);
            return;
        }

        $nota_fiscal = isset($data->nota_fiscal) ? $data->nota_fiscal : null;

        try {
            $resultado = $this->despesa->update(
                $data->id,
                $data->nome_despesa,
                $data->valor,
                $data->data,
                $nota_fiscal
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Despesa atualizada com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao atualizar a despesa."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function getAllDespesas()
    {
        try {
            $despesas = $this->despesa->getAllDespesas();
            http_response_code(200);
            echo json_encode(["despesas" => $despesas]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function delete()
    {
        if ($this->user_cargo != 'gerente') {
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id)) {
            http_response_code(400);
            echo json_encode(["message" => "ID é obrigatório para excluir a despesa."]);
            return;
        }

        try {
            $resultado = $this->despesa->delete($data->id);

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Despesa excluída com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir a despesa."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }
}
?>
