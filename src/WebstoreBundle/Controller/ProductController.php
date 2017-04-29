<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebstoreBundle\Entity\Cart;
use WebstoreBundle\Entity\Comment;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Entity\Promotion;
use WebstoreBundle\Form\CommentType;
use WebstoreBundle\Form\ProductType;
use WebstoreBundle\Service\SortProducts;

class ProductController extends Controller
{

    /**
     * @Route("products/add/", name="products_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addProductAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $oldPic = $product->getImage();
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setOwner($this->getUser());
            $product->setAddedOn(new \DateTime());
            $imgPath = 'images/products/';
            $uploadService = $this->get('picture_upload');
            $uploadService->uploadPicture($product, $oldPic, $imgPath);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }
        return $this->render('product/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("products", name="products_all")
     * @param Request $request
     * @return Response
     */
    public function allProducts(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        /** @var SortProducts $sort_products */
        $sort = $this->get('sort_products');
        $sort = $sort->sort($request);

        $calc = $this->get('price_calculator');
        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)->findAllAvailableQuery()->orderBy($sort[0], $sort[1]),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );

        return $this->render("product/all.html.twig", ['products' => $products, 'max_promotion' => $max_promotion, 'calc' => $calc]);
    }


    /**
     * @Route("products/view/{id}/", name="product_view")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOneAction(Request $request, Product $product)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $calc = $this->get('price_calculator');
        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->render('product/view.html.twig',
            ['product' => $product,
            'form' => $form->createView(),
                'max_promotion' => $max_promotion,
                'calc' => $calc
            ]);
    }

    /**
     * @Method("POST")
     * @Route("products/delete/{id}", name="product_delete")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function delete(Product $product)
    {
        if ($product === null) {
            $this->addFlash('error', "Product doesn't exist!");
            return $this->redirectToRoute("products_all");
        }

        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $cartRepo = $em->getRepository(Cart::class);
        $productInCarts = $cartRepo->findBy(['product' => $product]);

        //remove from other people's carts before deleting
        foreach ($productInCarts as $productInCart) {
            $em->remove($productInCart);
            $em->flush();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        $this->addFlash('notice', "You deleted " . $product->getName() . " successfully!");

        return $this->redirectToRoute('products_all');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("comment/add/", name="add_comment")
     */
    public function addComment(Request $request)
    {
        return $this->render('comment.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("products/sell/{id}", name="sell_product")
     */
    public function sellProduct()
    {

    }
}