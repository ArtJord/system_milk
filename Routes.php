<?php
// Routes.php

class Routes
{
    private array $routes = [];

    
    public function add(string $method, string $path, callable|array $handler): void
    {
        $method = strtoupper($method);

        
        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([0-9a-zA-Z\-_]+)', $path);

       
        $pattern = '#^' . rtrim($regex, '/') . '/?$#D';

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

   
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue; 
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); 
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['message' => '404 - Página não encontrada']);
        return;
    }
}
