<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SingleArticleController extends AbstractController
{
    #[Route('/Blog/article/{slug}', name: 'app_single_article')]
    public function index(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {

        //new commentaire lié à l'article

        $commentaire = new Commentaire();
        $commentaire->setArticle($article);

        $commentaire->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(CommentType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->addCommentaire($commentaire);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_single_article', [
                'slug' => $article->getSlug(),
            ]);
        }

        return $this->render('home/fiche_blog.html.twig', [
            'article' => $article,
            'form' => $form
        ]);
    }
}
