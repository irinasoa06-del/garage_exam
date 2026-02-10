<?php
// ===== RADICAL CORS FIX - HEADERS MUST COME FIRST =====
// Set CORS headers BEFORE any other code to ensure they're always sent
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-Token');
header('Access-Control-Max-Age: 86400');

// Handle OPTIONS (preflight) requests immediately with explicit success
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// NOW start output buffering for JSON cleanup
ob_start();

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

// Aggressively clean output buffer if it's a JSON response to remove any leading whitespace
// Check multiple conditions to be safe
$contentType = $response->headers->get('Content-Type');
if (strpos($contentType, 'application/json') !== false || $request->is('api/*') || $request->expectsJson()) {
    if (ob_get_length()) {
        ob_clean();
    }
} else {
    ob_end_flush(); // Flush if not JSON
}

$response->send();

$kernel->terminate($request, $response);
