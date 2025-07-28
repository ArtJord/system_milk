<?php 

namespace App\Controllers;

use App\model\usuario;
use Exception;


class usuarioController
{
    private $usuario;

    public function __construct($db)
    {
        $this->usuario = new Usuario($db);
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"));

        if(!isset($data->nome) || !isset($data->email) || !isset($data->senha) || !isset($data->cargo)){
            http_response_code(400);
            echo json_encode(["message" => "Nome, email, senha e cargo são obrigatórios"]);
            return;
        }

        try{
            $resultado = $this->usuario->create(
                $data->nome, 
                $data->email,
                $data->senha,
                $data->cargo,
                $data->telefone ?? null,
                $data->endereco ?? null,
                $data->cidade ?? null,
                $data->estado ?? null,
                $data->cep ?? null
            );
            
            if($resultado){
                http_response_code(200);
                echo json_encode(["message" => "Usuário criado com sucesso"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar o usuário"]);
            }
        } catch (Exception $e){
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }

     public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->email) || !isset($data->senha)) {
            http_response_code(400);
            echo json_encode(["message" => "Email e senha são obrigatórios."]);
            return;
        }

        try {
            $usuario = $this->usuario->login($data->email, $data->senha);

            if ($usuario) {
                http_response_code(200);
                echo json_encode([
                    "message" => "Login realizado com sucesso.",
                    "usuario" => $usuario
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Credenciais inválidas."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        }
    }


    // Verificar se o usuário tem permissão para editar ou excluir
    public function verificarPermissao($id_usuario, $cargo_necessario)
    {
        try {
            if ($this->usuario->verificarCargo($id_usuario, $cargo_necessario)) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erro: " . $e->getMessage()]);
            return false;
        }
    }
}