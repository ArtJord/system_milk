<?php
// Routes.php

class Routes
{
    private array $routes = [];

    /**
     * Registra uma rota.
     * Ex.: add('GET', '/lucro/{id}', [$controller, 'getById'])
     */
    public function add(string $method, string $path, callable|array $handler): void
    {
        $method = strtoupper($method);

        // Converte {param} em grupo de captura. Aceita números, letras, hífen e underscore.
        // Ex.: /lucro/{id} -> /lucro/([0-9a-zA-Z\-_]+)
        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([0-9a-zA-Z\-_]+)', $path);

        // Opcional: permitir barra final
        $pattern = '#^' . rtrim($regex, '/') . '/?$#D';

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    /**
     * Despacha a requisição.
     * Passe o método e o caminho já normalizado (você já faz isso no index.php).
     */
    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue; // método não bate; tenta próxima rota
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // remove match completo
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['message' => '404 - Página não encontrada']);
        return;
    }
}
