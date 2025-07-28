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

    // Login com verificação de senha
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

    public function findById($id){
        $stmt = $this->pdo->prepare("
        SELECT id, nome, email, cargo, telefone, endereco, cidade, estado, cep FROM usuarios WHERE id = ?
        
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarPerfil(){

    }
}