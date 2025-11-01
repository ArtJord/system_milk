<?php

namespace App\model;

use PDO;


class usuario
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    // Criação de usuário com campos opcionais
    public function create($nome, $email, $senha, $cargo, $telefone = null, $endereco = null, $cidade = null, $estado = null, $cep = null)
    {
        $senha_criptografada = password_hash($senha, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, cargo, telefone, endereco, cidade, estado, cep)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");



        return $stmt->execute([
            $nome,
            $email,
            $senha_criptografada,
            $cargo,
            $telefone,
            $endereco,
            $cidade,
            $estado,
            $cep
        ]);
    }


    public function login($email, $senha)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }


    public function verificarCargo($id, $cargo_necessario)
    {
        $stmt = $this->pdo->prepare("SELECT cargo FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario && $usuario['cargo'] === $cargo_necessario;
    }

    
    public function findById($id)
{
    $stmt = $this->pdo->prepare("
        SELECT id, nome, email, cargo, telefone, endereco, cidade, estado, cep, ativo
        FROM usuarios
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    
    public function atualizarPerfil($id, $telefone = null, $endereco = null, $cidade = null, $estado = null, $cep = null)
    {
        $stmt = $this->pdo->prepare("
        UPDATE usuarios
        SET telefone = ?, endereco = ?, cidade = ?, estado = ?, cep = ?
        WHERE id = ?
    ");

        return $stmt->execute([
            $telefone,
            $endereco,
            $cidade,
            $estado,
            $cep,
            $id
        ]);
    }

  public function getById(int $id): ?array
{
    $st = $this->pdo->prepare("
        SELECT id, nome, email, cargo, telefone, endereco, cidade, estado, cep, ativo
        FROM usuarios
        WHERE id = ?
    ");
    $st->execute([$id]);
    return $st->fetch(\PDO::FETCH_ASSOC) ?: null;
}

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        if ($ignoreId) {
            $st = $this->pdo->prepare("SELECT 1 FROM usuarios WHERE email = ? AND id <> ? LIMIT 1");
            $st->execute([$email, $ignoreId]);
        } else {
            $st = $this->pdo->prepare("SELECT 1 FROM usuarios WHERE email = ? LIMIT 1");
            $st->execute([$email]);
        }
        return (bool)$st->fetchColumn();
    }

    public function updateBasic(int $id, string $nome, string $email): bool
    {
        $st = $this->pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        return $st->execute([$nome, $email, $id]);
    }

    public function checkPassword(int $id, string $plain): bool
    {
        $st = $this->pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $st->execute([$id]);
        $hash = $st->fetchColumn();
        return $hash ? password_verify($plain, $hash) : false;
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $st = $this->pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        return $st->execute([$hash, $id]);
    }

    public function getAllUsers(): array
{
    $st = $this->pdo->prepare("
        SELECT id, nome, email, cargo, ativo, ultimo_login, created_at
        FROM usuarios
        ORDER BY nome ASC
    ");
    $st->execute();
    return $st->fetchAll(\PDO::FETCH_ASSOC);
}

public function countAll(): int
{
    $st = $this->pdo->query("SELECT COUNT(*) FROM usuarios");
    return (int)$st->fetchColumn();
}

public function update(int $id, array $campos): bool
{
    if ($id <= 0) {
        throw new \InvalidArgumentException("ID inválido");
    }

    // Whitelist dos campos que podem ser atualizados
    $permitidos = [
        'nome', 'email', 'telefone', 'endereco', 'cidade', 'estado', 'cep',
        'cargo', 'ativo'
    ];

    $sets = [];
    $params = [':id' => $id];

    foreach ($campos as $coluna => $valor) {
        if (!in_array($coluna, $permitidos, true)) {
            continue;
        }

        // Normalizações básicas
        if ($coluna === 'estado' && $valor !== null) {
            $valor = strtoupper(preg_replace('/[^A-Za-z]/', '', (string)$valor));
            $valor = substr($valor, 0, 2);
        }
        if ($coluna === 'ativo') {
            $valor = (int)!empty($valor);
        }
        if ($coluna === 'cargo' && $valor !== null) {
            $valor = strtolower((string)$valor);
            if (!in_array($valor, ['gerente','administrador','funcionario'], true)) {
                throw new \RuntimeException("Cargo inválido.");
            }
        }
        if ($coluna === 'email' && $valor !== null) {
            if ($this->emailExists((string)$valor, $id)) {
                throw new \RuntimeException("Email já está em uso.");
            }
        }

        $sets[] = "{$coluna} = :{$coluna}";
        $params[":{$coluna}"] = $valor;
    }

    if (empty($sets)) {
        return false; 
    }

    $sql = "UPDATE usuarios SET " . implode(', ', $sets) . " WHERE id = :id";
    $st  = $this->pdo->prepare($sql);
    return $st->execute($params);
}

public function findByIdWithSenha(int $id): ?array {
    $sql = "SELECT id, nome, email, cargo, senha, ativo FROM usuarios WHERE id = :id LIMIT 1";
    $st  = $this->pdo->prepare($sql); 
    $st->execute([':id' => $id]);
    $u = $st->fetch(PDO::FETCH_ASSOC);
    return $u ?: null;
}

}
