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
use Symfony\Contracts\Translation\TranslatorInterface;



class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index_for_heroku")
     */
    public function index(TranslatorInterface $translator): Response
    {  
        return $this->redirectToRoute('home');
    }
    
    /**
     * @Route("/{_locale}", name="home")
     */
    public function home(TranslatorInterface $translator): Response
    {  
        //$translated = $translator->trans('blog.add');

        $posts = $this->getDoctrine()
        ->getRepository(Post::class)
        ->findAll();

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/{_locale}/post/{slug}", name="post_show")
     */
    public function postShowById($slug, PostRepository $postRepository): Response
    {   
        // get list of post from database
        $post = $postRepository
        ->findOneBy(array('slug' => $slug));

        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{_locale}/search", name="post_search")
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

