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

        if (!isset($data->nome) || !isset($data->email) || !isset($data->senha) || !isset($data->cargo)) {
            http_response_code(400);
            echo json_encode(["message" => "Nome, email, senha e cargo são obrigatórios"]);
            return;
        }

        try {
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

            if ($resultado) {
                http_response_code(200);
                echo json_encode(["message" => "Usuário criado com sucesso"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar o usuário"]);
            }
        } catch (Exception $e) {
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
            ], JSON_UNESCAPED_UNICODE);
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

    public function me()
    {
        try {
            // Lê o Authorization de forma robusta (funciona em PHP embutido, Apache, Nginx etc.)
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
            if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
                http_response_code(401);
                echo json_encode(['message' => 'Token ausente']);
                return;
            }
            $cfg = require __DIR__ . '/../../../config/jwt.php';
            $decoded = \Firebase\JWT\JWT::decode($m[1], new \Firebase\JWT\Key($cfg['secret'], 'HS256'));

            // caminho do jwt.php (2 níveis acima de Controllers)
            $cfg = require __DIR__ . '/../../../config/jwt.php';
            $decoded = \Firebase\JWT\JWT::decode($m[1], new \Firebase\JWT\Key($cfg['secret'], 'HS256'));

            $id = (int)($decoded->sub ?? 0);
            if ($id <= 0) {
                http_response_code(401);
                echo json_encode(['message' => 'Token inválido']);
                return;
            }

            $u = $this->usuario->getById($id);
            if (!$u) {
                http_response_code(404);
                echo json_encode(['message' => 'Usuário não encontrado']);
                return;
            }

            echo json_encode([
                'id'       => (int)$u['id'],
                'fullName' => $u['nome'],
                'email'    => $u['email'],
                'cargo'    => $u['cargo'] ?? null,
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao obter perfil', 'detail' => $e->getMessage()]);
        }
    }

    public function updateBasic($id)
    {
        try {
            $id = (int)$id;
            $body = json_decode(file_get_contents('php://input'));
            if (!$id || !$body) {
                http_response_code(400);
                echo json_encode(['message' => 'Requisição inválida']);
                return;
            }

            $u = $this->usuario->getById($id);
            if (!$u) {
                http_response_code(404);
                echo json_encode(['message' => 'Usuário não encontrado']);
                return;
            }

            $fullName = isset($body->fullName) ? trim((string)$body->fullName) : $u['nome'];
            $email    = isset($body->email)    ? trim((string)$body->email)    : $u['email'];

            // Se email mudou, checar duplicidade
            if (strcasecmp($email, $u['email']) !== 0) {
                if ($this->usuario->emailExists($email)) {
                    http_response_code(409);
                    echo json_encode(['message' => 'Email já está em uso.']);
                    return;
                }
            }

            // Atualiza nome/email (se alterados)
            if ($fullName !== $u['nome'] || $email !== $u['email']) {
                $this->usuario->updateBasic($id, $fullName, $email);
            }

            // Se vier troca de senha, exige currentPassword e newPassword
            if (isset($body->currentPassword) || isset($body->newPassword)) {
                $current = (string)($body->currentPassword ?? '');
                $new     = (string)($body->newPassword     ?? '');

                if ($current === '' || $new === '') {
                    http_response_code(400);
                    echo json_encode(['message' => 'Para trocar a senha informe a senha atual e a nova senha.']);
                    return;
                }

                if (!$this->usuario->checkPassword($id, $current)) {
                    http_response_code(401);
                    echo json_encode(['message' => 'Senha atual incorreta.']);
                    return;
                }

                $hash = password_hash($new, PASSWORD_BCRYPT);
                $this->usuario->updatePassword($id, $hash);
            }

            $updated = $this->usuario->getById($id);
            echo json_encode([
                'message'  => 'Alterações salvas com sucesso.',
                'fullName' => $updated['nome'],
                'email'    => $updated['email'],
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao salvar alterações', 'detail' => $e->getMessage()]);
        }
    }
}
