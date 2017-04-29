<?php

namespace WebstoreBundle\Service;

use WebstoreBundle\Entity\Product;
use WebstoreBundle\Entity\User;

class ValidationService
{
    private $userValidation;

    private $container;

    public function checkIfOwner(Product $product, User $user)
    {
        if ($product->getOwner() === $user) {
            $this->controller->addFlash('error', "You already own this product!");
            return $this->redirectToRoute('products_all');
        }
        return false;
    }
}