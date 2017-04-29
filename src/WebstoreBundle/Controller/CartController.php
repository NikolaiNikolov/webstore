<?php

namespace WebstoreBundle\Controller;

use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use WebstoreBundle\Entity\Cart;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Entity\Transaction;

class CartController extends Controller
{
    /**
     * @param Product $product
     * @Route("cart/", name="user_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cart()
    {
        $carts = $this->getDoctrine()->getRepository(Cart::class)
            ->findAllQuery($this->getUser()->getId());
        $products = array_map(function ($p) {
            return $p->getProduct();
        }, $carts);
        $total = 0;

        $calc = $this->get('price_calculator');
        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        foreach ($carts as $cart) {
            /** @var Cart $cartProduct */
            $cartProduct = $cart;
            $productPrice = $calc->calculate($cartProduct->getProduct())['price'];
            $total += $cart->getQuantity() * $productPrice;
        }

        return $this->render('cart/cart.html.twig',
            ['products' => $carts, 'total' => $total,
            'max_promotion' => $max_promotion,
                'calc' => $calc]);
    }

    /**
     * @Route("cart/add/{id}", name="add_to_cart" )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Method("POST")
     * @param Product $product
     * @return Response
     */
    public function addToCartAction($id)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($id);

        if (is_null($product)) {
            $this->addFlash('error', "This product doesn't exist!");
            return $this->redirectToRoute('products_all');
        }

        if ($product->getOwner() === $this->getUser()) {
            $this->addFlash('error', "You already own this product!");
            return $this->redirectToRoute('products_all');
        }

        if (!$product->isAvailable()) {
            $this->addFlash('error', "This product is not for sale!");
            return $this->redirectToRoute('products_all');
        }

        //check if the product is already in the cart
        /** @var Cart $cartProduct */
        $cartProduct = $this->getDoctrine()->getRepository(Cart::class)
            ->findOneBy(['product' => $product, 'user' => $this->getUser()]);
        if ($cartProduct) {
            //if the product is in the cart increase its quantity by 1
            $cartProduct->setQuantity($cartProduct->getQuantity() + 1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($cartProduct);
            $em->flush();
            return $this->redirectToRoute('user_cart');
        }
        $cart = new Cart($this->getUser(), $product);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();
        return $this->redirectToRoute('user_cart');
    }

    /**
     * @param Product $product
     * @Method("POST")
     * @Route("cart/remove/{id}", name="remove_from_cart")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove($id)
    {
        $cartProduct = $this->getDoctrine()->getRepository(Cart::class)
            ->findOneBy(['product' => $id, 'user' => $this->getUser()]);

        if (is_null($cartProduct)) {
            return $this->redirectToRoute('user_cart');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($cartProduct);
        $em->flush();
        $this->addFlash('notice', "You removed " . $cartProduct->getProduct()->getName() . " from your cart successfully!");
        return $this->redirectToRoute('user_cart');
    }

    /**
     * @param Request $request
     * @param $id
     * @internal param Product $product
     * @Method("POST")
     * @Route("cart/update/{id}", name="update_cart")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $repo = $this->getDoctrine()->getRepository(Cart::class);
        $cartProduct = $repo->findOneBy(['product' => $id, 'user' => $this->getUser()]);
        $newQuantity = $request->request->get('quantity');

        if (is_null($cartProduct)) {
            return $this->redirectToRoute('user_cart');
        }
        if ($newQuantity > 0) {
            $cartProduct->setQuantity($newQuantity);
            $em = $this->getDoctrine()->getManager();
            $em->persist($cartProduct);
            $em->flush();
        } else {
            $this->addFlash('error', 'Quantity can\' be lower than 1');
        }


        return $this->redirectToRoute('user_cart');
    }

    /**
     * @Route("cart/checkout/", name="checkout_cart")
     * @Method("POST")
     */
    public function checkoutCartAction()
    {
        $cartRepo = $this->getDoctrine()->getRepository(Cart::class);
        $cart = $cartRepo->findAllQueryByUser($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        foreach ($cart as $items) {
            $cartProduct = $items->getProduct();
            $requestedQuantity = $items->getQuantity();
            $repo = $this->getDoctrine()->getRepository(Product::class);
            $product = $repo->find($cartProduct);
            $requestedQuantity = $items->getQuantity();

            $calc = $this->get('price_calculator');
            $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

            //initialize transaction
            try {
                $transaction = new Transaction($calc, $product, $this->getUser(), $em, $requestedQuantity);
                $transaction->initTransaction();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('user_cart');
            }

        }
        return $this->redirectToRoute('user_inventory');
    }
}
