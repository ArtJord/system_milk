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

        // 🚫 BLOQUEIO AQUI: usuário desativado não pode gerar token
        if (!isset($usuario['ativo']) || (int)$usuario['ativo'] !== 1) {
            http_response_code(401);
            echo json_encode(["message" => "Usuário desativado. Contate o administrador."]);
            return;
        }

        $cfg = require dirname(__DIR__, 3) . '/config/jwt.php';

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

        $token = \Firebase\JWT\JWT::encode($payload, $cfg['secret'], 'HS256');

        $safeUser = [
            'id'       => $usuario['id'],
            'nome'     => $usuario['nome'] ?? null,
            'email'    => $usuario['email'],
            'cargo'    => $usuario['cargo'] ?? null,
            'telefone' => $usuario['telefone'] ?? null
        ];

        http_response_code(200);
        echo json_encode([
            "message"   => "Login realizado com sucesso.",
            "token"     => $token,
            "expira_em" => $payload['exp'],
            "usuario"   => $safeUser
        ], JSON_UNESCAPED_UNICODE);
        return;

    } catch (\Exception $e) {
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
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
            if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
                http_response_code(401);
                echo json_encode(['message' => 'Token ausente']);
                return;
            }

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
                'telefone' => $u['telefone'] ?? null,
                'endereco' => $u['endereco'] ?? null,
                'cidade'   => $u['cidade'] ?? null,
                'estado'   => $u['estado'] ?? null,
                'cep'      => $u['cep'] ?? null,
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao obter perfil', 'detail' => $e->getMessage()]);
        }
    }

    public function updateSelf()
{
    try {
        // Auth (mesmo padrão do me())
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            http_response_code(401);
            echo json_encode(['message' => 'Token ausente']);
            return;
        }
        $cfg = require __DIR__ . '/../../../config/jwt.php';
        $decoded = \Firebase\JWT\JWT::decode($m[1], new \Firebase\JWT\Key($cfg['secret'], 'HS256'));
        $id = (int)($decoded->sub ?? 0);
        if ($id <= 0) {
            http_response_code(401);
            echo json_encode(['message' => 'Token inválido']);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $fullName = trim($body['fullName'] ?? '');
        $email    = trim($body['email'] ?? '');

        if ($fullName === '' || $email === '') {
            http_response_code(400);
            echo json_encode(['message' => 'Nome e email são obrigatórios.']);
            return;
        }

        // Evita colisão de email
        $u = $this->usuario->getById($id);
        if (!$u) {
            http_response_code(404);
            echo json_encode(['message' => 'Usuário não encontrado']);
            return;
        }
        if (strcasecmp($email, $u['email']) !== 0 && $this->usuario->emailExists($email)) {
            http_response_code(409);
            echo json_encode(['message' => 'Email já está em uso.']);
            return;
        }

        $this->usuario->updateBasic($id, $fullName, $email);

        $updated = $this->usuario->getById($id);
        echo json_encode([
            'id'       => (int)$updated['id'],
            'fullName' => $updated['nome'],
            'email'    => $updated['email'],
        ]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao atualizar perfil', 'detail' => $e->getMessage()]);
    }
}

public function updateSelfPassword()
{
    try {
        // Auth
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
            http_response_code(401);
            echo json_encode(['message' => 'Token ausente']);
            return;
        }
        $cfg = require __DIR__ . '/../../../config/jwt.php';
        $decoded = \Firebase\JWT\JWT::decode($m[1], new \Firebase\JWT\Key($cfg['secret'], 'HS256'));
        $id = (int)($decoded->sub ?? 0);
        if ($id <= 0) {
            http_response_code(401);
            echo json_encode(['message' => 'Token inválido']);
            return;
        }

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $current = (string)($body['currentPassword'] ?? '');
        $next    = (string)($body['newPassword'] ?? '');

        if ($current === '' || $next === '') {
            http_response_code(400);
            echo json_encode(['message' => 'Senha atual e nova senha são obrigatórias.']);
            return;
        }
        if (strlen($next) < 6) {
            http_response_code(422);
            echo json_encode(['message' => 'A nova senha deve ter ao menos 6 caracteres.']);
            return;
        }

        $u = $this->usuario->getByIdWithPassword($id);
        if (!$u) {
            http_response_code(404);
            echo json_encode(['message' => 'Usuário não encontrado']);
            return;
        }
        if (!password_verify($current, $u['senha'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Senha atual incorreta.']);
            return;
        }

        $hash = password_hash($next, PASSWORD_DEFAULT);
        $this->usuario->updateSenha($id, $hash);

        echo json_encode(['message' => 'Senha alterada com sucesso.']);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao alterar senha', 'detail' => $e->getMessage()]);
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


            if (strcasecmp($email, $u['email']) !== 0) {
                if ($this->usuario->emailExists($email)) {
                    http_response_code(409);
                    echo json_encode(['message' => 'Email já está em uso.']);
                    return;
                }
            }


            if ($fullName !== $u['nome'] || $email !== $u['email']) {
                $this->usuario->updateBasic($id, $fullName, $email);
            }


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

    public function getAllUsers()
{
    try {
        $rows = $this->usuario->getAllUsers();

        $users = array_map(function ($u) {
            return [
                'id'          => (int)$u['id'],
                'fullName'    => $u['nome'],
                'email'       => $u['email'],
                'cargo'       => $u['cargo'],
                'ativo'       => isset($u['ativo']) ? (int)$u['ativo'] : 1,
                'ultimoLogin' => $u['ultimo_login'] ?? null,
                'criadoEm'    => $u['created_at'] ?? null,
            ];
        }, $rows);

        echo json_encode($users);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao listar usuários', 'detail' => $e->getMessage()]);
    }
}

    public function publicRegister()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome, $data->email, $data->senha)) {
            http_response_code(400);
            echo json_encode(["message" => "Nome, email e senha são obrigatórios."]);
            return;
        }

        $nome  = trim((string)$data->nome);
        $email = trim((string)$data->email);
        $senha = (string)$data->senha;

        if (strlen($nome) < 2) {
            http_response_code(422);
            echo json_encode(["message" => "Informe um nome válido."]);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(["message" => "Email inválido."]);
            return;
        }
        if (strlen($senha) < 6) {
            http_response_code(422);
            echo json_encode(["message" => "A senha deve ter ao menos 6 caracteres."]);
            return;
        }

        try {

            $total = $this->usuario->countAll();
            $cargo = $total === 0 ? 'gerente' : 'funcionario';

            $ok = $this->usuario->create($nome, $email, $senha, $cargo);
            if (!$ok) {
                http_response_code(500);
                echo json_encode(["message" => "Erro ao criar usuário."]);
                return;
            }

            http_response_code(201);
            echo json_encode([
                "message" => "Cadastro realizado com sucesso.",
                "cargo"   => $cargo
            ]);
        } catch (\Throwable $e) {

            http_response_code(409);
            echo json_encode(["message" => "Email já está em uso.", "detail" => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        try {
            $id = (int)$id;
            $body = json_decode(file_get_contents('php://input'), true) ?: [];

            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['message' => 'ID inválido']);
                return;
            }

            // --- Auth via JWT (mesmo padrão do método me()) ---
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
            if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
                http_response_code(401);
                echo json_encode(['message' => 'Token ausente']);
                return;
            }
            $cfg = require __DIR__ . '/../../../config/jwt.php';
            $decoded = JWT::decode($m[1], new Key($cfg['secret'], 'HS256'));

            $meId   = (int)($decoded->sub ?? 0);
            $meRole = strtolower($decoded->cargo ?? 'usuario');
            $isManager = in_array($meRole, ['gerente', 'administrador'], true);

            if ($meId <= 0) {
                http_response_code(401);
                echo json_encode(['message' => 'Token inválido']);
                return;
            }


            $querAlterarPoder = array_key_exists('cargo', $body) || array_key_exists('ativo', $body);
            if ($querAlterarPoder && !$isManager) {
                http_response_code(403);
                echo json_encode(['message' => 'Sem permissão para alterar cargo/status.']);
                return;
            }


            if ($meId === $id && isset($body['cargo']) && strtolower($body['cargo']) === 'funcionario' && $isManager) {
                http_response_code(400);
                echo json_encode(['message' => 'Você não pode rebaixar o próprio cargo para "funcionário".']);
                return;
            }


            $ok = $this->usuario->update($id, $body);
            if (!$ok) {
                http_response_code(400);
                echo json_encode(['message' => 'Nenhum campo válido foi enviado.']);
                return;
            }

            echo json_encode(['ok' => true]);
        } catch (\RuntimeException $e) {
            http_response_code(409);
            echo json_encode(['message' => $e->getMessage()]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao atualizar', 'detail' => $e->getMessage()]);
        }
    }

    public function getOne($id)
    {
        try {
            $id = (int)$id;
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['message' => 'ID inválido']);
                return;
            }
            $u = $this->usuario->getById($id);
            if (!$u) {
                http_response_code(404);
                echo json_encode(['message' => 'Usuário não encontrado']);
                return;
            }
            echo json_encode($u);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Erro ao obter usuário', 'detail' => $e->getMessage()]);
        }
    }


    public function toggleAtivo($id)
    {
        try {
            $body = json_decode(file_get_contents('php://input'));
            $novo = isset($body->ativo) ? (int)$body->ativo : null;

            if ($novo !== 0 && $novo !== 1) {
                http_response_code(400);
                echo json_encode(['message' => 'Valor inválido para ativo. Use 0 ou 1.']);
                return;
            }

            // autenticação via token
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            $auth = $headers['Authorization'] ?? $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
            if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
                http_response_code(401);
                echo json_encode(['message' => 'Token ausente']);
                return;
            }

            $cfg = require __DIR__ . '/../../../config/jwt.php';
            $decoded = JWT::decode($m[1], new Key($cfg['secret'], 'HS256'));

            $meId   = (int)($decoded->sub ?? 0);
            $meRole = strtolower($decoded->cargo ?? 'usuario');
            $isManager = in_array($meRole, ['gerente', 'administrador'], true);

            if (!$isManager) {
                http_response_code(403);
                echo json_encode(['message' => 'Sem permissão.']);
                return;
            }

            // não pode se auto-desativar
            if ($meId === (int)$id && $novo === 0) {
                http_response_code(403);
                echo json_encode(['message' => 'Você não pode desativar a própria conta.']);
                return;
            }

            $ok = $this->usuario->update($id, ['ativo' => $novo]);
            if (!$ok) {
                http_response_code(404);
                echo json_encode(['message' => 'Usuário não encontrado ou falha ao atualizar.']);
                return;
            }

            echo json_encode(['message' => 'Status atualizado com sucesso.', 'ativo' => $novo]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Erro ao atualizar status',
                'detail'  => $e->getMessage()
            ]);
        }
    }

    private function getBearerToken(): ?string
    {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$hdr && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strtolower($k) === 'authorization') {
                    $hdr = $v;
                    break;
                }
            }
        }
        if (!$hdr) return null;
        if (preg_match('/Bearer\s+(.+)/i', $hdr, $m)) return trim($m[1]);
        return null;
    }

   public function alterarStatus($id)
{
    header('Content-Type: application/json');

    $idAlvo = (int)$id;   // ← id vem direto do router
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? [];

    if (!isset($body['ativo']) || !isset($body['password'])) {
        http_response_code(422);
        echo json_encode(['error' => 'Campos obrigatórios ausentes (ativo, password).']);
        return;
    }

    $novoAtivo     = (int)$body['ativo'] === 1 ? 1 : 0;
    $senhaDigitada = (string)$body['password'];

    $token = $this->getBearerToken();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado.']);
        return;
    }

    $cfg = require dirname(__DIR__, 3) . '/config/jwt.php';
    try {
        $payload = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($cfg['secret'], 'HS256'));
    } catch (\Throwable $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido.']);
        return;
    }

    $idLogado = isset($payload->sub) ? (int)$payload->sub : 0;
    $cargo    = isset($payload->cargo) ? (string)$payload->cargo : 'atendente';

    if ($idLogado <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado.']);
        return;
    }
    if (!in_array($cargo, ['gerente','administrador'], true)) {
        http_response_code(403);
        echo json_encode(['error' => 'Acesso negado.']);
        return;
    }

    $usuarioLogado = $this->usuario->findByIdWithSenha($idLogado);
    if (!$usuarioLogado || empty($usuarioLogado['senha']) || !password_verify($senhaDigitada, $usuarioLogado['senha'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Senha do aprovador inválida.']);
        return;
    }

    if ($idLogado === $idAlvo && $novoAtivo === 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Você não pode desativar o próprio usuário.']);
        return;
    }

    $ok = $this->usuario->update($idAlvo, ['ativo' => $novoAtivo]);
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['error' => 'Falha ao atualizar o status do usuário.']);
        return;
    }

    echo json_encode(['success' => true, 'ativo' => $novoAtivo], JSON_UNESCAPED_UNICODE);
}
}
