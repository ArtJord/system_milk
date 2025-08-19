<?php

namespace App\Controllers;

use App\model\Lucro; // Se a pasta for "Model", troque para use App\Model\Lucro;
use PDO;
use Exception;

class LucroController
{
    private $lucro;
    private $user_cargo;

    public function __construct(PDO $db, string $user_cargo)
    {
        $this->lucro = new Lucro($db);
        $this->user_cargo = $user_cargo;
    }

    // POST /lucro
    public function create(): void
    {
        $data = json_decode(file_get_contents("php://input"));

        try {
            $ok = $this->lucro->create(
                $data->data_receita    ?? null,
                $data->categoria       ?? null,
                $data->fonte_receita   ?? null,
                $data->cliente         ?? null,
                $data->descricao       ?? null,
                $data->quantidade      ?? null,
                $data->preco_unitario  ?? null,
                $data->valor_total     ?? null,
                $data->nota_fiscal     ?? null,
                $data->metodo_pagamento?? null,
                $data->status_pagamento?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento  ?? null,
                $data->observacoes     ?? null
            );

            if ($ok) {
                http_response_code(201);
                echo json_encode(["message" => "Lucro registrado com sucesso."]);
                return;
            }
            http_response_code(500);
            echo json_encode(["message" => "Erro ao registrar o lucro."]);
            return;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return;
        }
    }

    // PUT /lucro/{id}
    public function update($id): void
    {
        $data = json_decode(file_get_contents("php://input")) ?: (object)[];

        try {
            $ok = $this->lucro->update(
                $id, // <- usa o path param
                $data->data_receita    ?? null,
                $data->categoria       ?? null,
                $data->fonte_receita   ?? null,
                $data->cliente         ?? null,
                $data->descricao       ?? null,
                $data->quantidade      ?? null,
                $data->preco_unitario  ?? null,
                $data->valor_total     ?? null,
                $data->nota_fiscal     ?? null,
                $data->metodo_pagamento?? null,
                $data->status_pagamento?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento  ?? null,
                $data->observacoes     ?? null
            );

            if ($ok) {
                http_response_code(200);
                echo json_encode(["message" => "Lucro atualizado com sucesso."]);
                return;
            }
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar o lucro."]);
            return;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return;
        }
    }

    // GET /lucro/{id}
    public function getById($id): void
    {
        try {
            $row = $this->lucro->getById($id);

            if ($row) {
                http_response_code(200);
                echo json_encode(["lucro" => $row]);
                return;
            }
            http_response_code(404);
            echo json_encode(["message" => "Lucro não encontrado."]);
            return;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return;
        }
    }

    // GET /lucros
    public function getAll(): void
    {
        try {
            // Se seu Model tiver getAllLucros(), use-o. Caso tenha getAll(), troque aqui.
            $rows = $this->lucro->getAllLucros();
            http_response_code(200);
            echo json_encode(["lucros" => $rows]);
            return;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return;
        }
    }

    // DELETE /lucro/{id}
    public function delete($id): void
    {
        if ($this->user_cargo !== 'gerente') {
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir."]);
            return;
        }

        try {
            $ok = $this->lucro->delete($id); // <- usa o path param

            if ($ok) {
                http_response_code(200);
                echo json_encode(["message" => "Lucro excluído com sucesso."]);
                return;
            }
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir o lucro."]);
            return;

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return;
        }
    }
}
