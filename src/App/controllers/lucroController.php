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

        try {
            $resultado = $this->lucro->create(
                $data->data_receita ?? null,
                $data->categoria ?? null,
                $data->fonte_receita ?? null,
                $data->cliente ?? null,
                $data->descricao ?? null,
                $data->quantidade ?? null,
                $data->preco_unitario ?? null,
                $data->valor_total ?? null,
                $data->nota_fiscal ?? null,
                $data->metodo_pagamento ?? null,
                $data->status_pagamento ?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento ?? null,
                $data->observacoes ?? null
            );

            if ($resultado) {
                http_response_code(201);
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

        if (!isset($data->id)) {
            http_response_code(400);
            echo json_encode(["message" => "ID é obrigatório para atualizar o lucro."]);
            return;
        }

        try {
            $resultado = $this->lucro->update(
                $data->id,
                $data->data_receita ?? null,
                $data->categoria ?? null,
                $data->fonte_receita ?? null,
                $data->cliente ?? null,
                $data->descricao ?? null,
                $data->quantidade ?? null,
                $data->preco_unitario ?? null,
                $data->valor_total ?? null,
                $data->nota_fiscal ?? null,
                $data->metodo_pagamento ?? null,
                $data->status_pagamento ?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento ?? null,
                $data->observacoes ?? null
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

    public function getById($id)
{
    try {
        $lucro = $this->lucro->getById($id);

        if ($lucro) {
            http_response_code(200);
            echo json_encode(["lucro" => $lucro]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Lucro não encontrado."]);
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
        if ($this->user_cargo !== 'gerente') {
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir."]);
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

