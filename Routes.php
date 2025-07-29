<?php

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class Routes
{
    private $routes = [];

    
    public function add($method, $path, $callback)
    {
        // Substituindo a variável {id} por um padrão de captura
        $path = preg_replace('/\{(\w+)\}/', '(\d+)', $path);
        $this->routes[] = [
            'method' => $method,
            'path' => "#^" . $path . "$#", // Usando o padrão preg_replace para correspondência
            'callback' => $callback
        ];
    }

    
    public function dispatch($requestedPath)
    {
        $requestedMethod = $_SERVER["REQUEST_METHOD"];
        
        foreach ($this->routes as $route) {
            
            if ($route['method'] === $requestedMethod && preg_match($route['path'], $requestedPath, $matches)) {
                
                array_shift($matches);
                
                return call_user_func_array($route['callback'], $matches);
            }
        }
        
        // Caso nenhuma rota seja encontrada
        echo "404 - Página não encontrada";
    }
}
