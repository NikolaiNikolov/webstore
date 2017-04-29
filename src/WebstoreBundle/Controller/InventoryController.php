<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Service\SortProducts;

class InventoryController extends Controller
{

    /**
     * @Route("inventory", name="user_inventory")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function viewInventory(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $paginator = $this->get('knp_paginator');
        /** @var SortProducts $sort_products */
        $sort = $this->get('sort_products');
        $sort = $sort->sort($request);

        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)
                ->findInventoryProducts($this->getUser())->orderBy($sort[0], $sort[1]),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );


        return $this->render('user/inventory.html.twig', ['products' => $products]);
    }
}
