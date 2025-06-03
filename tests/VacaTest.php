<?php

use PHPUnit\Framework\TestCase;
use App\Model\Vaca;

class VacaTest extends TestCase
{
    private $pdo;
    private $stmt;
    private $vaca;

    protected function setUp(): void
    {
         $this->stmt = $this->createMock(PDOStatement::class);

          $this->pdo = $this->createMock(PDO::class);

           $this->vaca = new Vaca($this->pdo);
    }

    public function testCreate()
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with("INSERT INTO vaca (numero, nome, raca, descricao) VALUES (?, ?, ?, ?)")
            ->willReturn($this->stmt);

            $this->stmt->expects($this->once())
            ->method('execute')
            ->with([123, 'Bela', 'Holandesa', 'Vaca leiteira'])
            ->willReturn(true);

        $result = $this->vaca->create(123, 'Bela', 'Holandesa', 'Vaca leiteira');
        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with("UPDATE vaca SET numero = ?, nome = ?, raca = ?, descricao = ? WHERE id = ?")
            ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
            ->method('execute')
            ->with([789, 'Luna', 'Jersey', 'Descrição nova', 1])
            ->willReturn(true);

        $result = $this->vaca->update(1, 789, 'Luna', 'Jersey', 'Descrição nova');
        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with("DELETE FROM vaca WHERE id = ?")
            ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
            ->method('execute')
            ->with([1])
            ->willReturn(true);

        $result = $this->vaca->delete(1);
        $this->assertTrue($result);
    }

    public function testFindAll()
    {
        $expectedData = [
            ['id' => 1, 'numero' => 101, 'nome' => 'Vaca1', 'raca' => 'Raca1', 'descricao' => 'Descricao1'],
            ['id' => 2, 'numero' => 102, 'nome' => 'Vaca2', 'raca' => 'Raca2', 'descricao' => 'Descricao2'],
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedData);

        $this->pdo->expects($this->once())
            ->method('query')
            ->with("SELECT * FROM vaca")
            ->willReturn($stmtMock);

        $result = $this->vaca->findAll();
        $this->assertEquals($expectedData, $result);
    }

     public function testFindById()
    {
        $expectedData = ['id' => 1, 'numero' => 101, 'nome' => 'Vaca1', 'raca' => 'Raca1', 'descricao' => 'Descricao1'];

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with("SELECT * FROM vaca WHERE id = :id")
            ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
            ->method('bindParam')
            ->with(':id', $this->anything(), PDO::PARAM_INT);

        $this->stmt->expects($this->once())
            ->method('execute');

        $this->stmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expectedData);

        $result = $this->vaca->findById(1);
        $this->assertEquals($expectedData, $result);
    }
}
