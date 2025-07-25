<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SingleArticleController extends AbstractController
{
    #[Route('/Blog/article/{id}', name: 'app_single_article')]
    public function index(Article $article): Response
    {
        return $this->render('home/fiche_blog.html.twig', [
            'article' => $article,
        ]);
    }
}
