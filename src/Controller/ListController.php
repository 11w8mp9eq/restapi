<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;

class ListController extends AbstractController
{
    #[Route('/lista', name: 'app_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        echo "<a href='logout'>Logout</a><br><br>";
        $posts = $doctrine->getRepository(Post::class)->findAll();;

        if (!$posts) {
            throw $this->createNotFoundException(
                'No post found!'
            );
        }

        return $this->render('list/index.html.twig', ['posts' => $posts]);

    }

    #[Route('/lista/{id}', name: 'app_delete')]
    public function remove(ManagerRegistry $doctrine, int $id): Response
    {

        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);


            $entityManager->remove($post);
            $entityManager->flush();

            return new Response("Post with id: ".$id." deleted! Go back to <a href='.'>/lista</a><br><br>");
    }
}
