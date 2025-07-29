<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;

 class CorsListener
 {
    private array $allowedOrigins = ['http://localhost:8081'];
    
    public function __construct()
    {
        error_log('CorsListener: constructor ejecutado');
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        
       /* if (!$event->isMainRequest()) {
            error_log('No es un evento principal');
            return;
        }*/
        error_log('CorsListener: onKernelRequest ejecutado');
        $request = $event->getRequest();
        $origin = $request->headers->get('Origin');
    
        if ($request->getMethod() === 'OPTIONS')
        {
            if ($origin && in_array($origin, $this->allowedOrigins, true)) 
            {
                $response = new Response('', 204);
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                //$response->headers->set('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
                
                $requestedHeaders = $request->headers->get('Access-Control-Request-Headers');
                if ($requestedHeaders) {
                    $response->headers->set('Access-Control-Allow-Headers', $requestedHeaders);
                } else {
                    $response->headers->set('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept,  Access-Control-Request-Method, Authorization');
                }
                
                $event->setResponse($response);
                $event->stopPropagation();
            }
            else 
            {
                $response = new Response('Origin not allowed', 403);
                $event->setResponse($response);
            }
            
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
                

        $request = $event->getRequest();
        $response = $event->getResponse();

        $origin = $request->headers->get('Origin');
                
        if ($origin && in_array($origin, $this->allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'DNT, X-User-token, Keep-Alive, User-Agent, X-API-KEY, Origin, X-Requested-With, If-Modified-Since, Content-Type, Cache-Control, Accept, Access-Control-Request-Method, Authorization');
            $response->headers->set('Vary', 'Origin');
        }
    }

 }