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
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;



class AdminController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route("/{_locale}/admin/home", name="admin_home")
     */
    public function adminHome(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        // $posts = $this->getDoctrine()
        // ->getRepository(Post::class)
        // ->findAll();

        // return $this->render('admin/index.html.twig', [
        //     'posts' => $posts,
        // ]);


        $dql   = "SELECT a FROM App\Entity\Post a";
        $query = $em->createQuery($dql);

        //$query = $em->createQuery("SELECT p FROM App\Entity\Post p  WHERE p.content LIKE '%".$request->get('search')."%' OR p.title LIKE '%".$request->get('search')."%' ");

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        // parameters to template
        return $this->render('admin/index.html.twig', ['pagination' => $pagination]);


    }

    /**
     * @Route("/{_locale}/admin/post/add", name="admin_post_add")
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
     * @Route("/{_locale}/admin/post/edit/{slug}", name="admin_post_edit")
     */
    public function edit(Request $request, $slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(array('slug' => $slug));
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
     * @Route("/{_locale}/admin/post/delete/{slug}", name="admin_post_delete")
     */
    public function delete(Request $request, $slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(array('slug' => $slug));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('admin_home');
    }

    /**
     * @Route("/{_locale}/admin/user/add", name="admin_user_register")
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
