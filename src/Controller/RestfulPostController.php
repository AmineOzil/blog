<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class RestfulPostController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/{_locale}/api/rest/post/", name="restful_post_publish", methods={"GET"})
     */
    public function produceRestAPi(PostRepository $postRepository,  EntityManagerInterface $em): Response
    {
        //$posts = $postRepository->findAll($limit  = 1);
        $query = $em->createQuery("SELECT p FROM App\Entity\Post p  ORDER BY p.datePublication DESC");
        $query->setMaxResults(5);
        $posts= $query->getResult();
        return $this->json($posts, 200, [], ['groups'=> 'post:read']);                  
    }

    /**
     * @Route("/{_locale}/api/rest/post/consume", name="restful_post_consume", methods={"GET"})
     */
    public function consumeRestApi(): Response
    {
        $response = $this->client->request(
            'GET',
            'http://dummy.restapiexample.com/api/v1/employees'
        );

        $statusCode = $response->getStatusCode();
        //$contentType = $response->getHeaders()['content-type'][0];
        //$content = $response->getContent();
        $content = $response->toArray();
        $content = $content['data'];

        
        return $this->render('rest/index.html.twig', [
            'content' => $content,
        ]);

    
                  
    }
}