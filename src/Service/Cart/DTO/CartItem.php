<?php

declare(strict_types=1);

namespace App\Service\Cart\DTO;

use App\Entity\Product;

final readonly class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity
    ) {}

    public function subtotal(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }
}
