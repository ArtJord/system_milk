<?php

use PHPUnit\Framework\TestCase;
use App\model\usuario;

class UsuarioTest extends TestCase
{
    private $pdo;
    private $usuario;

    protected function setUp(): void
{
    
    $this->pdo = new PDO('sqlite::memory:');
    $this->pdo->exec("
        CREATE TABLE usuarios (
            id INTEGER PRIMARY KEY,
            nome TEXT,
            email TEXT,
            senha TEXT,
            cargo TEXT,
            telefone TEXT,
            endereco TEXT,
            cidade TEXT,
            estado TEXT,
            cep TEXT
        )
    ");

    $this->usuario = new usuario($this->pdo);
}

    public function testCreateUsuario()
    {
        $result = $this->usuario->create('João', 'joao@email.com', '123456', 'gerente');
        $this->assertTrue($result);
    }

    public function testLoginComCredenciaisValidas()
{
    $this->usuario->create('Maria', 'maria@email.com', 'senha123', 'funcionario');

    $usuarioLogado = $this->usuario->login('maria@email.com', 'senha123');

    $this->assertIsArray($usuarioLogado);
    $this->assertEquals('Maria', $usuarioLogado['nome']);
}

public function testLoginComSenhaErrada()
{
    $this->usuario->create('Carlos', 'carlos@email.com', 'abc123', 'administrador');

    $usuarioLogado = $this->usuario->login('carlos@email.com', 'senhaErrada');

    $this->assertFalse($usuarioLogado);
}

public function testVerificarCargo()
{
    $this->pdo->prepare("INSERT INTO usuarios (id, nome, email, senha, cargo) VALUES (1, 'Ana', 'ana@email.com', 'senha', 'gerente')")->execute();

    $temPermissao = $this->usuario->verificarCargo(1, 'gerente');

    $this->assertTrue($temPermissao);
}



}
