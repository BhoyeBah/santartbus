<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/panier', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        // dd($cartService->getTotal());
        return $this->render('home/cart.html.twig', [
            'panier' => $cartService->getFullCart(),
            'subtotal' => $cartService->getTotal(),
            'fullQuantity' => $cartService->fullQuantity(),
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_cart_add')]
    public function add(int $id, CartService $cartService)
    {

        $cartService->add($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease(int $id, CartService $cartService)
    {
        $cartService->decrease($id);
        return $this->redirectToRoute('app_cart');
    }


    #[Route('/panier/remove/', name: 'app_cart_remove')]
    public function remove(CartService $cartService)
    {
        $cartService->remove();
        return $this->redirectToRoute('app_cart');
    }

   
}
