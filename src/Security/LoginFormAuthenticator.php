<?php

namespace App\Security;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
//use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Routing\RouterInterface;

use Firebase\JWT\JWT;

use App\Entity\TKUser;
use App\Repository\TKUserRepository;

class LoginFormAuthenticator extends AbstractAuthenticator  implements AuthenticationEntryPointInterface
{
    private TKUserRepository $userRepository;
    private RouterInterface $router;
    
    public function __construct(TKUserRepository $repository, RouterInterface $router, private readonly string $jwtSecretKey) {

        $this->userRepository = $repository;
        $this->router = $router;
        $this->secretKey = $jwtSecretKey;
        
    }

    public function supports(Request $request): ?bool
    {
        return ($request->getPathInfo() === '/login' && $request->isMethod('POST'));
    }

    public function authenticate(Request $request): Passport
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Invalid JSON');
        }
   
        //$email = $request->getPayLoad()->get('user');
        //$password = $request->getPayLoad()->get('password');
        $email = $data['user'] ?? null;
        $password = $data['password'] ?? null;
        
        if (!$email || !$password) {
            throw new BadRequestHttpException('Missing credentials');
        }

        return new Passport( 
            new UserBadge($email),
            new PasswordCredentials($password)
            );
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // We set here the user, but this should be made in the background by the authenticate() method.
        $email = $request->getPayLoad()->get('user');
        $password = $request->getPayLoad()->get('password');
        
        
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $myToken = 'Bearer ' . JWT::encode(
            ['email' => $email,
                'expiration' => time() + 3600 // 1 hour expiration
                ], 
            $this->secretKey, 'HS256');

        // Prepare the response
        //$res = new JsonResponse(['user' => ($user->getFirstName() . ' ' . $user->getSecondName())], Response::HTTP_OK);
        $res = new JsonResponse(['email' => $user->getEmail(), 'firstName' => $user->getFirstName(), 'secondName' => $user->getSecondName()], Response::HTTP_OK);

        // Set the cookie
        $res->headers->setCookie(new Cookie("authorization",    // Cookie name
             $myToken,                                          // Cookie content
             time() + 86400,                                    // Cookie expiration date
             "/",                                               // Path
             null,                                              // Domain ('localhost' for local deploy, null for remote deploy)
             true,                                              // Secure
             true,                                              // HttpOnly
             false,                                              // Raw
             'None'));                                          // SameSite policy

        return $res;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        
        return  new JsonResponse("NO_OK", Response:: HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {

         /*
         * If you would like this class to control what happens when an anonymous user accesses a
         * protected page (e.g. redirect to /login), uncomment this method and make this class
         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
         *
         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
         */
        return  new JsonResponse("Unauthorized access.", Response:: HTTP_UNAUTHORIZED);
        /*return new RedirectResponse(
            $this->router->generate('login_app')
        );*/
    }
    
}


