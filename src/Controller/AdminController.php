<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PostFormType;
use App\Form\UserFormType;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function adminHome(Request $request): Response
    {       
        $posts = $this->getDoctrine()
        ->getRepository(Post::class)
        ->findAll();

        return $this->render('admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/admin/post/add", name="admin_post_add")
     */
    public function add(Request $request): Response
    {       
       
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setDatePublication(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('admin_home');
        }
        

        return $this->render('post/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/post/edit/{id}", name="admin_post_edit")
     */
    public function edit(Request $request, $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setDatePublication(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }
        
        return $this->render('post/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/post/delete/{id}", name="admin_post_delete")
     */
    public function delete(Request $request, $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        
        return $this->redirectToRoute('admin_home');
    }

    /**
     * @Route("/admin/user/add", name="admin_user_register")
     */
    public function useAdd(Request $request, PostRepository $postRepository): Response
    {

        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setRoles(['ROLE_ADMIN']);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        

        return $this->render('admin/register.html.twig', [
            'form' => $form->createView(),
        ]);

        /*
        $user = new User();
        $user->setEmail('karim');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'karim'));
        $user->setRoles(['ROLE_ADMIN']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_home');
        */
    }


    
}
