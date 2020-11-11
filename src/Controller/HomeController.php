<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PostFormType;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {   
        // get list of post from database
        $posts = $this->getDoctrine()
        ->getRepository(Post::class)
        ->findAll();

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show")
     */
    public function postShowById($id, PostRepository $postRepository): Response
    {   
        // get list of post from database
        $post = $postRepository
        ->find($id);

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/search", name="post_search")
     */
    public function searchPost(Request $request, EntityManagerInterface $em): Response
    {   
        $query = $em->createQuery("SELECT p FROM App\Entity\Post p  WHERE p.content LIKE '%".$request->get('search')."%' OR p.title LIKE '%".$request->get('search')."%' ");
        $posts= $query->getResult();

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'search' => $request->get('search')
        ]);
    }
}

