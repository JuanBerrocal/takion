<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Routing\RouterInterface;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UnexpectedValueException;

use App\Entity\TKUser;
use App\Repository\TKUserRepository;

class ApiTokenAuthenticator extends AbstractAuthenticator 
{

    public function __construct(TKUserRepository $repository, RouterInterface $router, private readonly string $jwtSecretKey) {

        $this->userRepository = $repository;
        $this->router = $router;
        $this->secretKey = $jwtSecretKey;
        
    }

    public function supports(Request $request): ?bool
    {
        // Always checks the incoming requests.
        return $request->cookies->has('authorization');
        //return true;
    }

    
    
    public function authenticate(Request $request): Passport
    {
        $cookie = $request->cookies->get('authorization');
        if (!$request->cookies->has('authorization') || 0 != strpos($cookie, 'Bearer ')) 
        {

            throw new CustomUserMessageAuthenticationException('No API token provided'); 
        }


       try { 

            $tokenBody = substr($cookie, 7);  // Erases the initial Bearer
        
            $decoded = JWT::decode($tokenBody, new Key($this->secretKey, 'HS256'));
            $decoded_array = (array) $decoded;
       }
       catch (LogicException $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage()); 
       }
       catch (UnexpectedValueException $e)  {
            throw new CustomUserMessageAuthenticationException($e->getMessage()); 

       }
       
            $user = $decoded_array['email'];
            $expiration = $decoded_array['expiration'];

        if (time() > $expiration) {
            throw new CustomUserMessageAuthenticationException('API token timed out'); 
        }
               
        return new SelfValidatingPassport(new UserBadge($user));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Does nothing, goes to the controller.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }  

}
