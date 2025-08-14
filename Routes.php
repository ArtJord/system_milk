<?php

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    http_response_code(204);
    exit;
}

class Routes
{
    private $routes = [];

    
    public function add($method, $path, $callback)
    {

        $method = strtoupper($method);

        $path = preg_replace('/\{(\w+)\}/', '(\d+)', $path);

        if(substr($path, -1) === '/'){
            $path = rtrim($path, '/');
        }
        $path .= '/?';

       
        $this->routes[] = [
            'method' => $method,
            'path' => "#^" . $path . "$#", 
            'callback' => $callback
        ];
    }

    
    public function dispatch($requestedPath)
    {
        $requestedMethod = $_SERVER["REQUEST_METHOD"];

        $requestedPath = parse_url($requestedPath, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            
            if ($route['method'] === $requestedMethod && preg_match($route['path'], $requestedPath, $matches)) {
                
                array_shift($matches);

                return call_user_func_array($route['callback'], $matches);
                
                
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        http_response_code((404));
        echo json_encode(["message" => "404 - Página não encontrada"]);
        exit;
        
    }
}
