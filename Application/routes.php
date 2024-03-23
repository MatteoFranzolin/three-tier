<?php

header('Access-Control-Allow-Origin: *'); // Consenti richieste da qualsiasi origine
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS'); // Consenti metodi HTTP specifici
header('Access-Control-Allow-Headers: Content-Type'); // Consenti header specifici
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Array associativo per mappare le route
$routes = [
    'GET' => [],
    'POST' => [],
    'PUT' => [],
    'DELETE' => []
];

// Aggiungi routes
function addRoute($method, $path, $callback)
{
    global $routes;
    $routes[$method][$path] = $callback;
}

// richiesta HTTP
function getRequestMethod()
{
    $_SERVER['HTTP_ACCEPT'] = 'application/vnd.api+json'; // CONTROLLA LA ACCEPT DELLA RESPONSE
    return $_SERVER['REQUEST_METHOD'];
}

// path
function getRequestPath()
{
    $path = $_SERVER['REQUEST_URI'];
    $path = parse_url($path, PHP_URL_PATH);
    return rtrim($path, '/');
}

// gestione richiesta
function handleRequest()
{
    global $routes;

    $method = getRequestMethod();
    $path = getRequestPath();

    if (isset($routes[$method])) {
        foreach ($routes[$method] as $routePath => $callback) {
            // Verifica se il percorso richiesto corrisponde al percorso della route
            if (preg_match('#^' . $routePath . '$#', $path, $matches)) {
                call_user_func_array($callback, $matches);
                return;
            }
        }
    }

    // 404 se la route non è stata trovata
    http_response_code(404);
    echo "404 Not Found";
}

//CONTROLLERS
require_once __DIR__ . '/app/controllers/ProductController.php';

//Aggiungi qui le routes

$productsPageCallback = function () {
    $controller = new ProductController();
    $controller->view();
};
$addProduct = function () {
    if (isset($_POST['data']))
        $params = $_POST;
    else
        $params = json_decode(file_get_contents("php://input"), true);

    $controller = new ProductController();
    $controller->add($params);
};
addRoute('GET', '/products', $productsPageCallback);
addRoute('POST', '/products', $addProduct);

$viewSingleProduct = function ($matches) {
    $parts = explode('/', $matches);
    $id = end($parts);
    $controller = new ProductController();
    $controller->viewProduct($id);
};

$deleteSingleProduct = function ($matches) {
    $parts = explode('/', $matches);
    $id = end($parts);
    $controller = new ProductController();
    $controller->deleteProduct($id);
};

$updateSingleProduct = function ($matches) {
    $parts = explode('/', $matches);
    $id = end($parts);
    $params = json_decode(file_get_contents("php://input"), true);
    $controller = new ProductController();
    $controller->updateProduct($id, $params);
};

addRoute('GET', '/products/(\d+)', $viewSingleProduct);
addRoute('DELETE', '/products/(\d+)', $deleteSingleProduct);
addRoute('PATCH', '/products/(\d+)', $updateSingleProduct);

// Gestore delle richieste
handleRequest();