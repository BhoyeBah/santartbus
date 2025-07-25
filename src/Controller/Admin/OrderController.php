<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/commande')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {

        return $this->render('admin/order/index.html.twig', [
            "orders" => $orderRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{order}', name: 'app_order_show', methods: ['GET', 'POST'])]
    public function orderDetail(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
