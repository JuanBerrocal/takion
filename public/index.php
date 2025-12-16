<?php


use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

// ** ONLY TO DEBUG
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


# No used any longer because Synfony tries to load .env file and symfony crashes when deployed in render.
# require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

require_once dirname(__DIR__).'/vendor/autoload.php';

$env = $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: false);
 
/*$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);*/

// ** ONLY TO DEBUG
// error_log('*** MENSAJE DE PRUEBA: index.php EJECUTADO ***'); 

try {
    $kernel = new Kernel($env, $debug);
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    // Log completo del error en var/log/prod.log
    $logFile = __DIR__ . '/../var/log/prod.log';
    $message = "[" . date('Y-m-d H:i:s') . "] ERROR CAPTURADO: " . $e->getMessage() . "\n"
             . $e->getTraceAsString() . "\n\n";
    file_put_contents($logFile, $message, FILE_APPEND);

    // También log a error_log de PHP
    error_log("*** ERROR CAPTURADO: " . $e->getMessage());

    // Enviar 500 con mensaje genérico
    http_response_code(500);
    echo "Internal Server Error. Revisa var/log/prod.log";
}

/*return function (array $context) {
    //return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    return new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
};*/
