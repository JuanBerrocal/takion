<?php

error_log('*** MENSAJE DE PRUEBA: index.php EJECUTADO ***'); 

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;


// ** ONLY FOR DEBUGGING
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

# No used any longer because Synfony tries to load .env file
# require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

require_once dirname(__DIR__).'/vendor/autoload.php';

$env = $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: 'prod';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: false);
 
$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

/*return function (array $context) {
    //return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    return new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
};*/
