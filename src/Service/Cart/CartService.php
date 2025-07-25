<?php

declare(strict_types=1);

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use App\Service\Cart\DTO\CartItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use RuntimeException;

final class CartService
{
    private readonly SessionInterface $session;

    public function __construct(
        RequestStack $requestStack,
        private readonly ProductRepository $productRepository
    ) {
        $request = $requestStack->getCurrentRequest();

        if (!$request || !$request->hasSession()) {
            throw new RuntimeException('No session available. Ensure this service is used during an HTTP request.');
        }

        $this->session = $request->getSession();

        if (!$this->session->isStarted()) {
            $this->session->start();
        }
    }

    /**
     * @return array<int, int>
     */
    private function getRawCart(): array
    {
        /** @var array<int, int> */
        return $this->session->get('panier', []);
    }

    private function saveCart(array $cart): void
    {
        $this->session->set('panier', $cart);
    }

    public function add(int $productId): void
    {
        $cart = $this->getRawCart();
        $cart[$productId] = ($cart[$productId] ?? 0) + 1;
        $this->saveCart($cart);
    }

    public function decrease(int $productId): void
    {
        $cart = $this->getRawCart();

        if (!isset($cart[$productId])) {
            return;
        }

        $cart[$productId]--;

        if ($cart[$productId] <= 0) {
            unset($cart[$productId]);
        }

        $this->saveCart($cart);
    }

    /**
     * @return CartItem[]
     */
    public function getFullCart(): array
    {
        $cart = $this->getRawCart();
        $items = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $items[] = new CartItem($product, $quantity);
        }

        return $items;
    }

    public function getTotal(): float
    {
        return array_reduce(
            $this->getFullCart(),
            static fn (float $total, CartItem $item): float => $total + $item->subtotal(),
            0.0
        );
    }

    public function fullQuantity(): int
    {
        return array_reduce(
            $this->getFullCart(),
            static fn (int $count, CartItem $item): int => $count + $item->quantity,
            0
        );
    }

    public function remove(): void
    {
        $this->session->remove('panier');
    }
}
