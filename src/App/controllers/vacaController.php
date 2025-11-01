<?php

namespace App\Controllers;

use App\Model\Vaca; 
use PDO;
use Exception;

class VacaController
{
    private $vaca;

    public function __construct($db)
    {
        $this->vaca = new Vaca($db);
    }


 public function create()
{
    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->numero_animal) || empty($data->numero_animal)) {
        http_response_code(400);
        echo json_encode(["message" => "O número é obrigatório."]);
        exit;
    }

    try {
        $resultado = $this->vaca->create(
            $data->numero_animal,
            $data->nome_animal ?? null,
            $data->raca ?? null,
            $data->sexo ?? null, 
            $data->data_nascimento ?? null,
            $data->peso_kg ?? null,
            $data->cor ?? null,
            $data->statuss ?? null,
            $data->estado_saude ?? null,
            $data->ultima_vacinacao ?? null,
            $data->proxima_vacinacao ?? null,
            $data->status_reprodutivo ?? null,
            $data->producao_diaria_litros ?? null,
            null,
            $data->observacoes ?? null,
            $data->criado_em ?? date("Y-m-d H:i:s")
        );

        if ($resultado) {
            http_response_code(201); 
            echo json_encode(["message" => "Animal cadastrada com sucesso."]);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao cadastrar o animal."]);
            exit;
        }

    } catch (\Throwable $e) {
        $msg = $e->getMessage();

    if (str_contains($msg, 'valor é muito longo')) {
        $userMsg = "Algum campo ultrapassou o limite permitido. Verifique o tamanho dos textos e tente novamente.";
    } elseif (str_contains($msg, 'violates not-null constraint')) {
        $userMsg = "Há campos obrigatórios não preenchidos. Preencha todos os campos marcados e tente novamente.";
    } elseif (str_contains($msg, 'invalid input syntax')) {
        $userMsg = "Um ou mais campos contêm dados inválidos. Corrija e tente novamente.";
    } else {
        $userMsg = "Ocorreu um erro inesperado ao salvar. Tente novamente mais tarde.";
    }

    http_response_code(400);
    echo json_encode(["message" => $userMsg]);
}
}


   public function update($id)
{
    header('Content-Type: application/json; charset=utf-8');

    
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    $data = is_array($json) ? $json : $_POST;

    try {
        
        $id = (int) $id;
        $atual = $this->vaca->findById($id);
        if (!$atual) {
            http_response_code(404);
            echo json_encode(['message' => 'Animal não encontrado.']);
            return;
        }

        
        $numero_animal = $data['numero_animal'] ?? $data['numero'] ?? $atual['numero_animal'] ?? null;

        
        $dados = [
            'numero_animal'          => $numero_animal,
            'nome_animal'            => $data['nome_animal']            ?? $data['nome']            ?? $atual['nome_animal'],
            'raca'                   => $data['raca']                   ?? $atual['raca'],
            'sexo'                   => $data['sexo']                   ?? $atual['sexo'],
            'data_nascimento'        => $data['data_nascimento']        ?? $atual['data_nascimento'],
            'peso_kg'                => $data['peso_kg']                ?? $atual['peso_kg'],
            'cor'                    => $data['cor']                    ?? $atual['cor'],
            'statuss'                => $data['statuss']                ?? $atual['statuss'],
            'estado_saude'           => $data['estado_saude']           ?? $atual['estado_saude'],
            'ultima_vacinacao'       => $data['ultima_vacinacao']       ?? $atual['ultima_vacinacao'],
            'proxima_vacinacao'      => $data['proxima_vacinacao']      ?? $atual['proxima_vacinacao'],
            'status_reprodutivo'     => $data['status_reprodutivo']     ?? $atual['status_reprodutivo'],
            'producao_diaria_litros' => $data['producao_diaria_litros'] ?? $atual['producao_diaria_litros'],
            'observacoes'            => $data['observacoes']            ?? $atual['observacoes'],
        ];

        
        $erros = [];
        if (empty($dados['numero_animal'])) $erros[] = 'O número é obrigatório.';
        if (empty($dados['nome_animal']))   $erros[] = 'O nome é obrigatório.';
        if (empty($dados['raca']))          $erros[] = 'A raça é obrigatória.';
        if (empty($dados['sexo']))          $erros[] = 'O sexo é obrigatório.';
        if (empty($dados['statuss']))       $erros[] = 'O status é obrigatório.';
        if ($erros) {
            http_response_code(400);
            echo json_encode(['message' => implode(' ', $erros)]);
            return;
        }

        
        if (is_string($dados['sexo'])) {
            $dados['sexo'] = strtoupper(trim($dados['sexo'])) === 'F' ? 'F' : 'M';
        }

        
        $ok = $this->vaca->updateParcial($id, $dados);

        if ($ok) {
            http_response_code(200);
            echo json_encode(['message' => 'Animal atualizado com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao atualizar o animal.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao atualizar o animal: ' . $e->getMessage()]);
    }
}



    public function delete($id)
    {
        try {
            $resultado = $this->vaca->delete($id);

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Animal deletada com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao deletar o animal."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao deletar animal: " . $e->getMessage()]);
        }
    }

    
    public function findAll()
    {
        $vacas = $this->vaca->findAll();

        if ($vacas) {
            http_response_code(200);
            echo json_encode($vacas);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Nenhum animal encontrado."]);
        }
    }

   
   
    public function findById($id)
{
    
    $id = (int) $id;

    try {
        
        $vaca = $this->vaca->findById($id);

        if ($vaca) {
            
            echo json_encode($vaca);
        } else {
            
            http_response_code(404);
            echo json_encode(["message" => "Animal não encontrado"]);
        }
    } catch (Exception $e) {
        
        http_response_code(500);
        echo json_encode(["message" => "Erro ao buscar animal: " . $e->getMessage()]);
    }
}


}
