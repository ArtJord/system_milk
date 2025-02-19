<?php

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Routes
{
    private $routes = [];

    // Método para adicionar novas rotas
    public function add($method, $path, $callback)
    {
        // Substituindo a variável {id} por um padrão de captura (\d+)
        $path = preg_replace('/\{(\w+)\}/', '(\d+)', $path);
        $this->routes[] = [
            'method' => $method,
            'path' => "#^" . $path . "$#", // Usando o padrão preg_replace para correspondência
            'callback' => $callback
        ];
    }

    // Método para disparar a rota correspondente com base no método e caminho da URL
    public function dispatch($requestedPath)
    {
        $requestedMethod = $_SERVER["REQUEST_METHOD"];
        
        foreach ($this->routes as $route) {
            // Verificando se o método e a rota correspondem
            if ($route['method'] === $requestedMethod && preg_match($route['path'], $requestedPath, $matches)) {
                // Removendo o primeiro item da array, pois matches[0] será a URL completa, não o parâmetro
                array_shift($matches);
                
                // Chamando a função de callback (controlador) e passando os parâmetros da URL
                return call_user_func_array($route['callback'], $matches);
            }
        }
        
        // Caso nenhuma rota seja encontrada
        echo "404 - Página não encontrada";
    }
}
