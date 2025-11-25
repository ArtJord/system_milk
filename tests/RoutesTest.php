<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../Routes.php';

class RoutesTest extends TestCase
{
    private Routes $routes;

    protected function setUp(): void
    {
        $this->routes = new Routes();
    }

    public function testRotaGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->routes->add('GET', '/usuario/{id}', function (string $id) {
            echo "Usuário com ID: $id";
        });


        ob_start();
        $this->routes->dispatch('GET', '/usuario/15');
        $output = ob_get_clean();

        $this->assertSame('Usuário com ID: 15', $output);
    }

    public function testRotaPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->routes->add('POST', '/usuario', function () {
            echo 'Usuário criado!';
        });

        ob_start();
        $this->routes->dispatch('POST', '/usuario');
        $output = ob_get_clean();

        $this->assertSame('Usuário criado!', $output);
    }

    public function testRotaInexistente()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        $this->routes->dispatch('GET', '/rota-nao-existe');
        $output = ob_get_clean();
        
        $data = json_decode($output, true);

        $this->assertIsArray($data);
        $this->assertSame('404 - Página não encontrada', $data['message'] ?? null);
    }
}
