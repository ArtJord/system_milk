<?php

use PHPUnit\Framework\TestCase;
use App\Model\Vaca;

class VacaTest extends TestCase
{
    private $pdo;
    private $vaca;

    protected function setUp(): void
    {
<<<<<<< HEAD
        $this->pdo = $this->createMock(PDO::class);
        $this->vaca = new Vaca($this->pdo);
    }

    public function testCreateFailure()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->willReturn(false);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->with($this->stringContains('INSERT INTO animais'))
                  ->willReturn($stmt);

        $result = $this->vaca->create(123);
        $this->assertFalse($result);
=======
        // Criando um mock do PDOStatement
        $statement = $this->createMock(PDOStatement::class);
        $statement->expects($this->once())
            ->method('execute')
            ->with([
                '123',                 // numero_animal
                'Mimosa',              // nome_animal
                'Holandesa',           // raca
                'Saudável e bem ativa',// observacoes
                1                      // id
            ])
            ->willReturn(true);

        // Criando um mock do PDO
        $this->pdo = $this->createMock(PDO::class);
        $this->pdo->method('prepare')->willReturn($statement);

        $this->vaca = new Vaca($this->pdo);
>>>>>>> c53e788 (fix: corrige o metodo de update no model e cotroller)
    }

    public function testDeleteFailure()
    {
<<<<<<< HEAD
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([999])
             ->willReturn(false);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->with('DELETE FROM animais WHERE id = ?')
                  ->willReturn($stmt);

        $result = $this->vaca->delete(999);
        $this->assertFalse($result);
    }

    public function testFindAllEmpty()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('fetchAll')
             ->with(PDO::FETCH_ASSOC)
             ->willReturn([]);

        $this->pdo->expects($this->once())
                  ->method('query')
                  ->with('SELECT * FROM animais')
                  ->willReturn($stmt);

        $result = $this->vaca->findAll();
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    public function testFindByIdNotFound()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('bindParam')
             ->with(':id', $this->anything(), PDO::PARAM_INT);
        $stmt->expects($this->once())
             ->method('execute');
        $stmt->expects($this->once())
             ->method('fetch')
             ->with(PDO::FETCH_ASSOC)
             ->willReturn(false);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->with('SELECT * FROM animais WHERE id = :id')
                  ->willReturn($stmt);

        $result = $this->vaca->findById(999);
        $this->assertFalse($result);
    }
=======
        $result = $this->vaca->update(1, '123', 'Mimosa', 'Holandesa', 'Saudável e bem ativa');
        $this->assertTrue($result);
    }
>>>>>>> c53e788 (fix: corrige o metodo de update no model e cotroller)
}
