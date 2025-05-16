<?php

namespace App\Controllers;

use App\model\Lucro;
use PDO;
use Exception;

class LucroController
{
    private $lucro;
    private $user_cargo;

    public function __construct($db, $user_cargo)
    {
        $this->lucro = new Lucro($db);
        $this->user_cargo = $user_cargo;
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->origem) || !isset($data->quantidade) || !isset($data->valor) || !isset($data->data)) {
            http_response_code(400);
            echo json_encode(["message" => "Origem, quantidade, valor e data são obrigatórios."]);
            return;
        }

        $tipo = isset($data->tipo) ? $data->tipo : null;
        $nota_fiscal = isset($data->nota_fiscal) ? $data->nota_fiscal : null;

        try {
            $resultado = $this->lucro->create(
                $data->origem,
                $data->quantidade,
                $data->valor,
                $data->data,
                $tipo,
                $nota_fiscal
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Lucro registrado com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao registrar o lucro."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function update()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id) || !isset($data->origem) || !isset($data->quantidade) || !isset($data->valor) || !isset($data->data)) {
            http_response_code(400);
            echo json_encode(["message" => "ID, origem, quantidade, valor e data são obrigatórios."]);
            return;
        }

        $tipo = isset($data->tipo) ? $data->tipo : null;
        $nota_fiscal = isset($data->nota_fiscal) ? $data->nota_fiscal : null;

        try {
            $resultado = $this->lucro->update(
                $data->id,
                $data->origem,
                $data->quantidade,
                $data->valor,
                $data->data,
                $tipo,
                $nota_fiscal
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Lucro atualizado com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao atualizar o lucro."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function getAllLucros()
    {
        try {
            $lucros = $this->lucro->getAllLucros();
            http_response_code(200);
            echo json_encode(["lucros" => $lucros]);
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
            echo json_encode(["message" => "ID é obrigatório para excluir o lucro."]);
            return;
        }

        try {
            $resultado = $this->lucro->delete($data->id);

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Lucro excluído com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao excluir o lucro."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }
}
?>
