<?php

namespace App\Controllers;

use App\model\Lucro; 
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

    public function setUserCargo($cargo) { $this->user_cargo = $cargo; }


    
    public function create(): void
    {
        $data = json_decode(file_get_contents("php://input")) ?: (object)[];

        try {
            // Validação simples (lado servidor) para mensagem amigável
            $todosVazios = true;
            foreach (['data_receita','categoria','fonte_receita','cliente','descricao','quantidade','preco_unitario','numero_nfe','metodo_pagamento','status_pagamento','data_vencimento','data_pagamento','observacoes'] as $k) {
                if (isset($data->$k) && $data->$k !== null && $data->$k !== '') { $todosVazios = false; break; }
            }
            if ($todosVazios) {
                http_response_code(400);
                echo json_encode(["message" => "Preencha os dados do lançamento para continuar."]);
                return;
            }
            // Usa a função antiga, que agora retorna o registro completo
            $novo = $this->lucro->create(
                $data->data_receita    ?? null,
                $data->categoria       ?? null,
                $data->fonte_receita   ?? null,
                $data->cliente         ?? null,
                $data->descricao       ?? null,
                $data->quantidade      ?? null,
                $data->preco_unitario  ?? null,
                $data->numero_nfe      ?? null,
                $data->metodo_pagamento?? null,
                $data->status_pagamento?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento  ?? null,
                $data->observacoes     ?? null
            );

           if ($novo) {
                http_response_code(201);
                header('Location: /lucro/' . $novo['id']);
                echo json_encode(["lucro" => $novo]);
                return;
            }

            http_response_code(500);
            echo json_encode(["message" => "Erro ao registrar o lucro."]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao processar sua solicitação."]);
        }
    }

    
    public function update($id): void
    {
        $data = json_decode(file_get_contents("php://input")) ?: (object)[];

        try {
            // Função antiga retorna registro atualizado
            $atualizado = $this->lucro->update(
                $id,
                $data->data_receita    ?? null,
                $data->categoria       ?? null,
                $data->fonte_receita   ?? null,
                $data->cliente         ?? null,
                $data->descricao       ?? null,
                $data->quantidade      ?? null,
                $data->preco_unitario  ?? null,
                $data->numero_nfe      ?? null,
                $data->metodo_pagamento?? null,
                $data->status_pagamento?? null,
                $data->data_vencimento ?? null,
                $data->data_pagamento  ?? null,
                $data->observacoes     ?? null
            );

            if ($atualizado) {
                http_response_code(200);
                echo json_encode(["lucro" => $atualizado]);
                return;
            }

            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar o lucro."]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao processar sua solicitação."]);
        }
    }

    
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

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao processar sua solicitação."]);
        }
    }

    
    public function getAll(): void
{
    try {
        $inicio    = $_GET['inicio']    ?? null;
        $fim       = $_GET['fim']       ?? null;
        $categoria = $_GET['categoria'] ?? null;

        $rows = $this->lucro->getAllLucros($inicio, $fim, $categoria);

         http_response_code(200);
        echo json_encode(["lucros" => $rows]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Falha ao processar sua solicitação."]);
    }
}
    
    public function delete($id): void
    {
        if (strtolower($this->user_cargo) !== 'gerente') {
            http_response_code(403);
            echo json_encode(["message" => "Apenas o gerente pode excluir."]);
            return;
        }

        try {
            $ok = $this->lucro->delete($id); 

            if ($ok) {
                http_response_code(200);
                echo json_encode(["message" => "Lucro excluído com sucesso."]);
                return;
            }

            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir o lucro."]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Falha ao processar sua solicitação."]);
        }
    }
}
