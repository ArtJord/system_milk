<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Routes.php';

class RoutesTest extends TestCase
{
    private $routes;

    protected function setUp(): void
    {
        $this->routes = new Routes();
    }

    public function testRotaGet()
    {
        // Define o método HTTP simulado
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->routes->add('GET', '/usuario/{id}', function ($id) {
            return "Usuário com ID: $id";
        });

        $response = $this->routes->dispatch('/usuario/15');

        $this->assertEquals("Usuário com ID: 15", $response);
    }

    public function testRotaPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->routes->add('POST', '/usuario', function () {
            return "Usuário criado!";
        });

        $response = $this->routes->dispatch('/usuario');

        $this->assertEquals("Usuário criado!", $response);
    }

    public function testRotaInexistente()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->routes->add('GET', '/teste', function () {
            return "Ok";
        });

        // Captura a saída (echo) da dispatch para rota não encontrada
        ob_start();
        $this->routes->dispatch('/rota-nao-existe');
        $output = ob_get_clean();

        $this->assertStringContainsString("404 - Página não encontrada", $output);
    }
}
