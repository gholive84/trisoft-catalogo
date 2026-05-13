<?php

declare(strict_types=1);

/**
 * Front controller único.
 * Todas as requisições passam por aqui (via .htaccess rewrite).
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;

// ---- Bootstrap ----
Config::boot(BASE_PATH);
Logger::setLogDir(BASE_PATH . '/storage/logs');

// Tratamento de erros
$debug = Config::isDebug();
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/php-errors.log');

set_exception_handler(function (Throwable $e) use ($debug) {
    Logger::error('Exceção não tratada', [
        'class' => get_class($e),
        'msg'   => $e->getMessage(),
        'file'  => $e->getFile(),
        'line'  => $e->getLine(),
    ]);
    if ($debug) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo "<pre style='font-family:monospace;padding:1rem;background:#fee;color:#900'>";
        echo htmlspecialchars(get_class($e) . ': ' . $e->getMessage() . "\n\n" . $e->getTraceAsString());
        echo "</pre>";
        exit;
    }
    Response::abort(500, 'Ocorreu um erro inesperado. Tente novamente.');
});

// Sessão
Session::start();

// ---- Roteamento ----
$router  = new Router();
$request = new Request();

require BASE_PATH . '/config/routes.php';

$result = $router->dispatch($request);

if (is_string($result)) {
    echo $result;
} elseif (is_array($result) || is_object($result)) {
    Response::json($result);
}
