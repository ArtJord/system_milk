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

    public function setUserCargo($cargo) { $this->user_cargo = $cargo; }


    public function create()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->data_despesa) || !isset($data->quantidade) || !isset($data->preco_unitario)) {
            http_response_code(400);
            echo json_encode(["message" => "Data da despesa, quantidade e preço unitário são obrigatórios."]);
            return;
        }

        
        $valor_total = $data->quantidade * $data->preco_unitario;

        try {
            $resultado = $this->despesa->create(
                $data->numero_despesa ?? null,
                $data->data_despesa,
                $data->prioridade ?? null,
                $data->categoria ?? null,
                $data->subcategoria ?? null,
                $data->descricao ?? null,
                $data->fornecedor ?? null,
                $data->quantidade,
                $data->preco_unitario,
                $valor_total,
                $data->numero_nfe ?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento ?? null,
                $data->observacoes ?? null
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

        if (!isset($data->id) || !isset($data->data_despesa) || !isset($data->quantidade) || !isset($data->preco_unitario)) {
            http_response_code(400);
            echo json_encode(["message" => "ID, data da despesa, quantidade e preço unitário são obrigatórios."]);
            return;
        }

        $valor_total = $data->quantidade * $data->preco_unitario;

        try {
            $resultado = $this->despesa->update(
                $data->id,
                $data->numero_despesa ?? null,
                $data->data_despesa,
                $data->prioridade ?? null,
                $data->categoria ?? null,
                $data->subcategoria ?? null,
                $data->descricao ?? null,
                $data->fornecedor ?? null,
                $data->quantidade,
                $data->preco_unitario,
                $valor_total,
                $data->numero_nfe ?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento ?? null,
                $data->observacoes ?? null
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

    public function getById($id)
    {
        try {
            $despesa = $this->despesa->getById($id);
            if ($despesa) {
                http_response_code(200);
                echo json_encode(["despesa" => $despesa]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Despesa não encontrada."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

    public function delete()
    {
        if ($this->user_cargo != 'gerente') {
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir."]);
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

