<?php

namespace App\Controllers;

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

    public function setUserCargo($cargo)
    {
        $this->user_cargo = $cargo;
    }


    public function create()
    {

        try {
            $data = json_decode(file_get_contents("php://input"));
            if (!$data) {
                http_response_code(400);
                echo json_encode(["message" => "Envie os dados da despesa."]);
                return;
            }

            // normalizar: "" -> null
            foreach ($data as $k => $v) {
                if (is_string($v) && trim($v) === "") $data->$k = null;
            }

            // validação
            $errors = [];
            if (empty($data->data_despesa))  $errors["data_despesa"] = "Data da despesa é obrigatória.";
            if (empty($data->fornecedor))    $errors["fornecedor"]   = "Informe o fornecedor.";
            $qtd = isset($data->quantidade) ? (float)$data->quantidade : 0;
            $pu  = isset($data->preco_unitario) ? (float)$data->preco_unitario : 0;
            if (!($qtd > 0 && $pu > 0))      $errors["preco"]        = "Informe quantidade e preço unitário maiores que zero.";

            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode(["message" => "Há campos inválidos.", "details" => $errors]);
                return;
            }

            // chama o model (ajuste se sua assinatura for diferente)
            $novo = $this->despesa->create(
                $data->numero_despesa ?? null,
                $data->data_despesa   ?? null,
                $data->prioridade     ?? null,
                $data->categoria      ?? null,
                $data->subcategoria   ?? null,
                $data->descricao      ?? null,
                $data->fornecedor     ?? null,
                $qtd,
                $pu,
                $data->numero_nfe     ?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento ?? null,
                $data->observacoes    ?? null
            );

            if ($novo) {
                http_response_code(201);
                echo json_encode(["despesa" => $novo]);
                return;
            }

            http_response_code(500);
            echo json_encode(["message" => "Erro ao registrar a despesa."]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao processar sua solicitação."]);
        }
    }

   

    
    public function update($id)
    {
       try {
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Envie os dados da despesa."]);
            return;
        }

        foreach ($data as $k => $v) {
            if (is_string($v) && trim($v) === "") $data->$k = null;
        }

        $errors = [];
        if (empty($data->data_despesa))  $errors["data_despesa"] = "Data da despesa é obrigatória.";
        if (empty($data->fornecedor))    $errors["fornecedor"]   = "Informe o fornecedor.";
        $qtd = isset($data->quantidade) ? (float)$data->quantidade : 0;
        $pu  = isset($data->preco_unitario) ? (float)$data->preco_unitario : 0;
        if (!($qtd > 0 && $pu > 0))      $errors["preco"]        = "Informe quantidade e preço unitário maiores que zero.";

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(["message" => "Há campos inválidos.", "details" => $errors]);
            return;
        }

        $ok = $this->despesa->update(
            $id,
            $data->numero_despesa ?? null,
            $data->data_despesa   ?? null,
            $data->prioridade     ?? null,
            $data->categoria      ?? null,
            $data->subcategoria   ?? null,
            $data->descricao      ?? null,
            $data->fornecedor     ?? null,
            $qtd,
            $pu,
            $data->numero_nfe     ?? null,
            $data->data_vencimento?? null,
            $data->data_pagamento ?? null,
            $data->observacoes    ?? null
        );

        if ($ok) {
            http_response_code(200);
            echo json_encode(["despesa" => $ok]);
            return;
        }

        http_response_code(500);
        echo json_encode(["message" => "Erro ao atualizar a despesa."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Falha ao processar sua solicitação."]);
    }
}

    public function getAllDespesas()
    {
        try {
            $inicio     = $_GET['inicio']     ?? null;
            $fim        = $_GET['fim']        ?? null;
            $categoria  = $_GET['categoria']  ?? null;
            $prioridade = $_GET['prioridade'] ?? null;

            $rows = $this->despesa->getAllDespesas($inicio, $fim, $categoria, $prioridade);

            http_response_code(200);
            echo json_encode(["despesas" => $rows]);
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

    public function delete($id = null)
    {
        // permissão
        if ($this->user_cargo !== 'gerente') {
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir."]);
            return;
        }

        // se não veio pela URL, tenta pegar do body
        if ($id === null) {
            $data = json_decode(file_get_contents("php://input"));
            if (isset($data->id)) {
                $id = (int)$data->id;
            }
        }

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID é obrigatório para excluir a despesa."]);
            return;
        }

        try {
            $ok = $this->despesa->delete($id);
            if ($ok) {
                http_response_code(200);
                echo json_encode(["message" => "Despesa excluída com sucesso."]);
                return;
            }
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir a despesa."]);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return;
        }
    }
}
