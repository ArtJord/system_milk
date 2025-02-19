<?php

require_once __DIR__ . '/../model/Vaca.php';

class VacaController
{
    private $vaca;

    public function __construct($db)
    {
        $this->vaca = new Vaca($db);  // Instancia o modelo Vaca
    }

    public function create()
    {
        // Captura os dados enviados na requisição
        $data = json_decode(file_get_contents("php://input"));

        // Verifica se o número foi enviado corretamente (obrigatório)
        if (isset($data->numero)) {
            try {
                
                $resultado = $this->vaca->create(
                    $data->numero,
                    isset($data->nome) ? $data->nome : null,
                    isset($data->raca) ? $data->raca : null,
                    isset($data->racao) ? $data->racao : null,
                    isset($data->remedio) ? $data->remedio : null,
                    isset($data->bezerro) ? $data->bezerro : null,
                    isset($data->descricao) ? $data->descricao : null
                );

                if ($resultado) {
                    http_response_code(200);
                    echo json_encode([
                        "message" => "Vaca cadastrada com sucesso.",
                        "data" => $data
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erro ao cadastrar vaca."]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao cadastrar vaca: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "O número é obrigatório."]);
        }
    }
}
