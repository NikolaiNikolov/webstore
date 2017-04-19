<?php

namespace WebstoreBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Entity\User;

class InventoryController extends Controller
{

    /**
     * @Route("user/{id}/inventory", name="view_inventory")
     */
    public function viewInventory($id)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findBy(['owner' => $id]);

        return $this->render('user/inventory.html.twig', ['products' => $products]);
    }
}
