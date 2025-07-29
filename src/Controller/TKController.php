<?php
namespace App\Controller;
use App\Entity\TKTopic;
use App\Entity\TKPost;
use App\Entity\TKUser;
use App\Repository\TKUserRepository;
use App\Repository\TKTopicRepository;
use App\Repository\TKPostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class TKController extends AbstractController {

    private $tkuserRepository;
    private $tktopicRepository;

    public function __construct(TKUserRepository $tkuserRepository, TKTopicRepository $tktopicRepository, TKPostRepository $tkpostRepository) {
        $this->tkuserRepository = $tkuserRepository;
        $this->tktopicRepository = $tktopicRepository;
        $this->tkpostRepository = $tkpostRepository;
    }

    #[Route('/login', name: 'login', methods:['POST'])]
    public function login() {
        
        // By the moment returns this. But the true work is done by Security/LoginFormAuthenticator
        $response = new Response('Login managed by authenticator', Response::HTTP_NO_CONTENT);
        $response->headers->set('X-Debug-Test', 'here');
        return $response;
    }

    #[Route('/logout', name: 'logout')]
    public function logout() {
        //die('This is the homepage');
        //return $this->render('tkuser/index.html.twig', []);

        // By the moment returns this. But the true work is done by Security/LoginFormAuthenticator
        $res = new JsonResponse("OK", Response::HTTP_OK);

        // Claers cookie.
        $res->headers->clearCookie('authorization');
        return $res;
    }

    #[Route('/home', name: 'home')]
    public function home() {
        //die('This is the homepage');
        //return $this->render('tkuser/index.html.twig', []);
        /*return new RedirectResponse(
            $this->router->generate('login_app')
        );*/
        return new JsonResponse(['status' => 'Home page'], Response::HTTP_OK);
    }

    #[Route('/adduser', name: 'create_tkuser')]
    public function createTKUser(Request $request): JsonResponse {
        $data = json_decode($request->getContent());

        $email = $data['email'];

        if (empty($email)) {
            throw new NotFoundHttpException('Expecting parameters');
        }

        $this->tkuserRepository->saveTKUser($email);

        return new JsonResponse(['status' => 'User created!'], Response:HTTP_CREATED);
    }

    #[Route('/getalluser', name: 'get_all_tkuser')]
    public function getAllTKUser(Request $request) {
        $tkusers = $this->tkuserRepository->findAll();
        $data = [];

        foreach ($tkusers as $tkuser) {
            $data[] = ['id' => $tkuser->getId(),
                'email' => $tkuser->getEmail()];
        }
    
        return  new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/topic', name: 'topic')]
    public function getTopics(Request $request) {
        $tktopics = $this->tktopicRepository->findAll();
        $data = [];

        foreach ($tktopics as $tktopic) {
            $data[] = ['id' => $tktopic->getId(),
                'subject' => $tktopic->getSubject()];
        }
    
        return  new JsonResponse($data, Response::HTTP_OK);
    }
    
    #[Route('/topicinsert', name: 'topic_insert')]
    public function createTKTopic(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $subject = $data['subject'];

        if (empty($subject)) {
            throw new NotFoundHttpException('Expecting parameters');
        }

        $post = $this->tktopicRepository->findOneBy(['subject' => $subject]);

        if ($post) {
            return new JsonResponse(['status' => 'Topics must be unique.'], Response::HTTP_NO_CONTENT);
        }
        else {
            $this->tktopicRepository->saveTKTopic($subject);
            return new JsonResponse(['status' => 'Topic created!'], Response::HTTP_OK);
        }
        
    }

    #[Route('/topicupdate/{id}', name: 'topic_update', methods:['POST'])]
    public function updateTKTopic(Request $request, string $id): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $id = $data['id'];
        $subject = $data['subject'];

        if ((empty($subject)) || empty($id)) {
            throw new NotFoundHttpException('Expecting parameters');
        }

        $topic = $this->tktopicRepository->find((int)$id);
        $topic->setSubject($subject);

        $this->tktopicRepository->updateTKTopic($topic);

        return new JsonResponse(['status' => 'Topic updated!'], Response::HTTP_OK);
    }
    
    #[Route('/topicdelete/{id}', name: 'topic_delete', methods:['DELETE'])]
    public function deleteTKTopic(Request $request, string $id): JsonResponse {
        
        if (empty($id)) {
            throw new NotFoundHttpException('Expecting parameters');
        }
        
        $topic = $this->tktopicRepository->find((int)$id);
                
        $post = $this->tkpostRepository->findOneBy(['subject' => $topic]);
        
        if ($post) {
            return new JsonResponse(['status' => 'Topic can not be deleted because exists in one post'], Response::HTTP_NO_CONTENT);
            //throw new NotFoundHttpException("Can't delete this topic, there are existing posts wiht this topic.");
        }
        
        $this->tktopicRepository->removeTKTopic($topic);
        return new JsonResponse(['status' => 'Topic deleted!'], Response::HTTP_OK);
    }

    #[Route('/topicread/{id}', name: 'topic_read', methods:['GET'])]
    public function readTKTopic(Request $request, string $id): JsonResponse {
        
        $tktopic = $this->tktopicRepository->find((int)$id);
        
        $data = ['id' => $tktopic->getId(),
        'subject' => $tktopic->getSubject()];
            
        return  new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/postlist', name: 'postlist')]
    public function getPosts(Request $request) {
        $tkposts = $this->tkpostRepository->findAll();
        $data = [];

        foreach ($tkposts as $tkpost) {
            $author = $this->tkuserRepository->find($tkpost->getAuthor());
            $subject = $this->tktopicRepository->find($tkpost->getSubject());

            $data[] = ['id' => $tkpost->getId(),
                'created' => $tkpost->getCreated()->format('Y-m-d H:i:s'),
                'updated' => $tkpost->getUpdated()->format('Y-m-d H:i:s'),
                'title' => $tkpost->getTitle(),
                'text' => $tkpost->getText(),
                'author' => ['email' => $author->getEmail(), 'firstName' => $author->getFirstName(), 'secondName' => $author->getSecondName()],
                'subject' => $subject->getSubject()];
        }
    
        return  new JsonResponse($data, Response::HTTP_OK);
    }
    
    #[Route('/postread/{id}', name: 'post_read', methods:['GET'])]
    public function readTKPost(Request $request, string $id): JsonResponse {
        
        $tkpost = $this->tkpostRepository->find((int)$id);
        
        $author = $this->tkuserRepository->find($tkpost->getAuthor());
        $subject = $this->tktopicRepository->find($tkpost->getSubject());

        $data = ['id' => $tkpost->getId(),
            'created' => $tkpost->getCreated()->format('Y-m-d H:i:s'),
            'updated' => $tkpost->getUpdated()->format('Y-m-d H:i:s'),
            'title' => $tkpost->getTitle(),
            'text' => $tkpost->getText(),
            'author' => ['email' => $author->getEmail(), 'firstName' => $author->getFirstName(), 'secondName' => $author->getSecondName()],
            'subject' => $subject->getSubject()];
            
        return  new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/postinsert', name: 'post_insert')]
    public function createTKPost(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['author']) || empty($data['title']) || empty($data['text']) || empty($data['subject'] || empty($data['created']) || empty($data['updated']))) {
            throw new NotFoundHttpException('Expecting parameters');
        }
      
        $this->tkpostRepository->saveTKPost($data);

        return new JsonResponse(['status' => 'Post created!'], Response::HTTP_OK);
    }
    
    #[Route('/postupdate/{id}', name: 'post_update', methods:['POST'])]
    public function updateTKPost(Request $request, string $id): JsonResponse {
        
        // DEBUG: Under contruction.
        $data = json_decode($request->getContent(), true);

        if (empty($data['id'] || $data['author']) || empty($data['title']) || empty($data['text']) || empty($data['subject'] || empty($data['created']) || empty($data['updated']))) {
            throw new NotFoundHttpException('Expecting parameters');
        }

        $post = $this->tkpostRepository->find((int)$id);
        if ($post) {

            $updatedDate = new \DateTime('@'.strtotime($data['updated']));
            $post->setUpdated($updatedDate);
            $post->setTitle($data['title']);
            $post->setText($data['text']);

            $newTopic = new TKTopic();
            $newTopic->setSubject($data['subject']);
            
            $post->setSubject($newTopic);
            
            $this->tkpostRepository->updateTKPost($post);
        }
        else {
            throw new NotFoundHttpException("Couldn't find this post to update it.");
        }
        
        return new JsonResponse(['status' => 'Post updated!'], Response::HTTP_OK);
    }
     
    #[Route('/postdelete/{id}', name: 'post_delete', methods:['DELETE'])]
    public function deleteTKPost(Request $request, string $id): JsonResponse {
        
        if (empty($id)) {
            throw new NotFoundHttpException('Expecting parameters');
        }
        
        $post = $this->tkpostRepository->find((int)$id);
        $this->tkpostRepository->removeTKPost($post);
        
        return new JsonResponse(['status' => 'Post deleted!'], Response::HTTP_OK);
    }



    /*public function getTopics(Request $request) {
        $tktopics = $this->tktopicRepository->findAll();
        $data = [];

        foreach ($tktopics as $tktopic) {
            $data[] = ['id' => $tktopic->getId(),
                'subject' => $tktopic->getSubject()];
        }
    
        return  new JsonResponse($data, Response::HTTP_OK);
    }*/
}

?>