<?php

require __DIR__ . '/vendor/autoload.php';

use App\Config\Database;
use Bramus\Router\Router;
use Symfony\Component\Dotenv\Dotenv;

// Initialize Router
$router = new Router();
$router->setBasePath('/api.php');

// Init Database
// Note: In Docker, ENV vars are passed directly, but we can assume defaults or load .env if exists
Database::init();

// Helper for JSON Response
function jsonResponse($data, $status = 200)
{
  header('Content-Type: application/json');
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit();
}

// Routes
$router->options('/.*', function () {
  // CORS handled by Nginx, just return 200
  http_response_code(200);
});

$router->get('/', function () {
  jsonResponse(['status' => 'ok', 'message' => 'Sunny Coffee API Running']);
});

// Load API Routes
require __DIR__ . '/src/Routes/api.php';

$router->run();
