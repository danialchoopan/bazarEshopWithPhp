<?php

namespace App\Config;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, array $handler, array $middleware = []): self
    {
        $this->addRoute('GET', $path, $handler, $middleware);
        return $this;
    }

    public function post(string $path, array $handler, array $middleware = []): self
    {
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): void
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Run middlewares
                foreach ($route['middleware'] as $middleware) {
                    if (is_array($middleware)) {
                        // [ClassName::class, 'methodName'] format
                        [$class, $method] = $middleware;
                        if (class_exists($class) && method_exists($class, $method)) {
                            call_user_func([$class, $method]);
                        }
                    } elseif (is_string($middleware)) {
                        if (method_exists($middleware, 'handle')) {
                            $middleware::handle();
                        }
                    } elseif (is_callable($middleware)) {
                        $middleware();
                    }
                }

                // Extract parameters (named groups)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Call controller method
                [$controllerClass, $action] = $route['handler'];

                if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
                    call_user_func_array([$controllerClass, $action], $params);
                    return;
                }
            }
        }

        // 404
        http_response_code(404);
        require dirname(__DIR__, 2) . '/resources/views/404.php';
    }
}
