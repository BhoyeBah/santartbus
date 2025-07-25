<?php

namespace App\Controller\Admin;

use App\Entity\CategoryArticle;
use App\Form\CategoryArticleType;
use App\Repository\CategoryArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/category/article')]
final class CategoryArticleController extends AbstractController
{
    #[Route(name: 'app_category_article_index', methods: ['GET'])]
    public function index(CategoryArticleRepository $categoryArticleRepository): Response
    {
        return $this->render('admin/category_article/index.html.twig', [
            'category_articles' => $categoryArticleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoryArticle = new CategoryArticle();
        $form = $this->createForm(CategoryArticleType::class, $categoryArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoryArticle);
            $entityManager->flush();

            return $this->redirectToRoute('app_category_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category_article/new.html.twig', [
            'category_article' => $categoryArticle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_article_show', methods: ['GET'])]
    public function show(CategoryArticle $categoryArticle): Response
    {
        return $this->render('admin/category_article/show.html.twig', [
            'category_article' => $categoryArticle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryArticle $categoryArticle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryArticleType::class, $categoryArticle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_category_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category_article/edit.html.twig', [
            'category_article' => $categoryArticle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_article_delete', methods: ['POST'])]
    public function delete(Request $request, CategoryArticle $categoryArticle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoryArticle->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categoryArticle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
