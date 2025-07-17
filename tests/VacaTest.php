<?php

use PHPUnit\Framework\TestCase;
use App\Model\Vaca;

class VacaTest extends TestCase
{
    private $pdo;
    private $vaca;

    protected function setUp(): void
    {
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
    }

    public function testUpdate()
    {
        $result = $this->vaca->update(1, '123', 'Mimosa', 'Holandesa', 'Saudável e bem ativa');
        $this->assertTrue($result);
    }
}
