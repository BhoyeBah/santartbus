<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/commande', name: 'app_order_create')]
    public function order(CartService $cartService, Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {

        $panier = $cartService->getFullCart();

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_home');
        }
        
        // Création de la commande
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //Dernier id utiliser
            $lastOrder = $entityManager->getRepository(Order::class)->findOneBy([], ['id' => 'DESC']);
            $lastId = $lastOrder ? $lastOrder->getId() : 0;

            $numero = sprintf('#CMD_%04d', $lastId + 1);

            try {
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setStatus(false);
                $order->setTotal($cartService->getTotal());
                $order->setNumero($numero);

                foreach ($panier as $item) {

                    $orderDetail = new OrderDetail();
                    $orderDetail->setQuantity($item->quantity);
                    $orderDetail->setPriceUnit($item->product->getPrice());
                    $orderDetail->setSubtotal($item->product->getPrice() * $item->quantity);
                    $orderDetail->setOrders($order);
                    $orderDetail->setProduct($item->product);

                    $entityManager->persist($orderDetail);
                    $order->addOrderDetail($orderDetail);
                }


                $entityManager->persist($order);
                $entityManager->flush();

                $numeroCommande = sprintf('#CMD_%04d', $order->getId());
                $order->setNumero($numeroCommande);

                $entityManager->flush();
                $cartService->remove();

                $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            } catch (\Throwable $th) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.');
            }
        }

        return $this->render('home/order.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'panier' => $panier,
            'total' => $cartService->getTotal(),
        ]);
    }
}
