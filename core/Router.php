<?php
/**
 * Router Class
 * Xu ly routing don gian cho du an
 */
class Router {
    
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * Dang ky route GET
     */
    public function get($path, $callback) {
        $this->addRoute('GET', $path, $callback);
    }
    
    /**
     * Dang ky route POST
     */
    public function post($path, $callback) {
        $this->addRoute('POST', $path, $callback);
    }
    
    /**
     * Them route vao danh sach
     */
    private function addRoute($method, $path, $callback) {
        // Chuyen doi path thanh regex pattern
        $pattern = $this->pathToPattern($path);
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }
    
    /**
     * Chuyen doi path thanh regex pattern
     * Vi du: /user/:id -> /user/([^/]+)
     */
    private function pathToPattern($path) {
        // Xu ly parameters dang :param
        $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $path);
        
        // Escape dau /
        $pattern = '#^' . $pattern . '$#';
        
        return $pattern;
    }
    
    /**
     * Xu ly request hien tai
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        
        // Tim route phu hop
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Lay parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Goi callback
                return $this->callCallback($route['callback'], $params);
            }
        }
        
        // Khong tim thay route
        return $this->callNotFound();
    }
    
    /**
     * Lay URI hien tai
     */
    private function getUri() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Loai bo query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Loai bo base path neu can
        $basePath = $this->getBasePath();
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        return $uri ?: '/';
    }
    
    /**
     * Lay base path tu APP_URL
     */
    private function getBasePath() {
        if (!defined('APP_URL')) {
            return '';
        }
        
        $parsed = parse_url(APP_URL);
        return isset($parsed['path']) ? rtrim($parsed['path'], '/') : '';
    }
    
    /**
     * Goi callback function
     */
    private function callCallback($callback, $params = []) {
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }
        
        // Neu la string dang "Controller@method"
        if (is_string($callback) && strpos($callback, '@') !== false) {
            list($controller, $method) = explode('@', $callback);
            
            // Require controller file
            $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
            
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller file not found: $controllerFile");
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controller)) {
                throw new Exception("Controller class not found: $controller");
            }
            
            $controllerObj = new $controller();
            
            if (!method_exists($controllerObj, $method)) {
                throw new Exception("Method $method not found in $controller");
            }
            
            return call_user_func_array([$controllerObj, $method], $params);
        }
        
        throw new Exception("Invalid callback");
    }
    
    /**
     * Set callback cho 404
     */
    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Goi callback 404
     */
    private function callNotFound() {
        http_response_code(404);
        
        if ($this->notFoundCallback) {
            return $this->callCallback($this->notFoundCallback);
        }
        
        // Default 404
        echo "404 - Page Not Found";
    }
    
    /**
     * Redirect den URL khac
     */
    public static function redirect($url, $statusCode = 302) {
        header("Location: $url", true, $statusCode);
        exit;
    }
    
    /**
     * Tao URL tu path
     */
    public static function url($path = '') {
        $baseUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
        $path = ltrim($path, '/');
        return $baseUrl . '/' . $path;
    }
}
?>