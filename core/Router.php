<?php
/**
 * ZimsecExamMate — Simple Router
 * 
 * Maps URLs to page files. Used when .htaccess routes
 * all requests through a single entry point.
 */

class Router
{
    private static array $routes = [];

    /**
     * Register a route
     */
    public static function add(string $pattern, string $file): void
    {
        self::$routes[$pattern] = $file;
    }

    /**
     * Resolve the current request to a file
     */
    public static function resolve(?string $uri = null): ?string
    {
        $uri = $uri ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $uri = trim($uri, '/');

        // Direct match
        if (isset(self::$routes[$uri])) {
            return self::$routes[$uri];
        }

        // Pattern matching
        foreach (self::$routes as $pattern => $file) {
            $regex = '#^' . preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $uri, $matches)) {
                // Store matched params where the target file can access them
                $_REQUEST['_route_params'] = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $file;
            }
        }

        return null;
    }

    /**
     * Dispatch the request
     */
    public static function dispatch(): void
    {
        $file = self::resolve();

        if ($file && file_exists(ROOT_DIR . '/' . $file)) {
            require ROOT_DIR . '/' . $file;
        } else {
            http_response_code(404);
            include TEMPLATES_DIR . '/error.php';
        }
    }
}