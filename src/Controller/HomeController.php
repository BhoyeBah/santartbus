<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ArticleRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository,ArticleRepository $articleRepository): Response
    {
        return $this->render('home/accueil.html.twig', [
            'products' => $productRepository->findByIshome(true),
            'articles' => $articleRepository->findBy([], ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }

    #[Route('produit', name: 'app_product')]
    public function product(ProductRepository $productRepository): Response
    {
        return $this->render('home/product.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('blog', name: 'app_blog', methods: ['POST', 'GET'])]
    public function blog(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginator): Response
    {
        $data = $articleRepository->findBy([], ['createdAt' => 'DESC']);
        $articles = $paginator->paginate($data, $request->query->getInt('page', 1), 6);
       
        return $this->render('home/blog.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('gallery', name: 'app_gallery')]
    public function gallery(): Response
    {
        return $this->render('home/gallery.html.twig');
    }

    #[Route('contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($contact);
            $entityManager->flush();
            // ✉️ Envoi de l’email
            $email = (new Email())
                ->from($contact->getEmail())
                ->to('support@santart.com')
                ->subject($contact->getSubject())
                ->text("Nom : " . $contact->getName() . "\n\nSujet : " . $contact->getSubject() . "\n\nMessage :\n" . $contact->getMessage());
            $mailer->send($email);

            $this->addFlash("success", "Votre message a été envoyé avec succée");
            return $this->redirectToRoute('app_contact');
        }
        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
