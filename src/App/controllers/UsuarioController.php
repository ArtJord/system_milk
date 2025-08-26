<?php 

namespace App\Controllers;

use App\model\usuario;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;




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

        if (!$usuario) {
            http_response_code(401);
            echo json_encode(["message" => "Credenciais inválidas."]);
            return;
        }

      //Carrega as configs do JWT:
        $cfg = require dirname(__DIR__, 3) . '/config/jwt.php';

        //Monta o payload e assina o toke
        $now = time();
        $payload = [
            'iss'   => $cfg['issuer'],
            'aud'   => $cfg['audience'],
            'iat'   => $now,
            'nbf'   => $now,
            'exp'   => $now + $cfg['expires_in'],
            'sub'   => (string)$usuario['id'],
            'email' => $usuario['email'],
            'cargo' => $usuario['cargo'] ?? 'atendente'
        ];

        $token = JWT::encode($payload, $cfg['secret'], 'HS256');

        
        $safeUser = [
            'id'     => $usuario['id'],
            'nome'   => $usuario['nome'] ?? null,
            'email'  => $usuario['email'],
            'cargo'  => $usuario['cargo'] ?? null,
            'telefone' => $usuario['telefone'] ?? null
        ];

        //Retorna o token e dados seguros do usuario
        http_response_code(200);
        echo json_encode([
            "message"   => "Login realizado com sucesso.",
            "token"     => $token,
            "expira_em" => $payload['exp'],
            "usuario"   => $safeUser
        ]);
        return;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Erro: " . $e->getMessage()]);
        return;
    }
}

     public function atualizarPerfil($id)
    {
        $data = json_decode(file_get_contents("php://input"));

        try {
            $resultado = $this->usuario->atualizarPerfil(
                $id,
                $data->telefone ?? null,
                $data->endereco ?? null,
                $data->cidade ?? null,
                $data->estado ?? null,
                $data->cep ?? null
            );

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Perfil atualizado com sucesso."]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao atualizar perfil."]);
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
