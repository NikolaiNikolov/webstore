<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Form\DeleteButtonType;
use WebstoreBundle\Form\ProductType;
use WebstoreBundle\Service\SortProducts;
use WebstoreBundle\Service\UploadPicture;

class ProductController extends Controller
{

    /**
     * @Route("products/add", name="products_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addProductAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product->setOwner($this->getUser());
            $product->setAddedOn(new \DateTime());

            $uploadService = $this->get('picture_upload');
            $uploadService->uploadPicture($product);

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

        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)->findAllAvailableQuery()->orderBy($sort[0], $sort[1]),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );

        return $this->render("product/all.html.twig", ['products' => $products]);
    }


    /**
     * @Route("products/view/{id}", name="product_view")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewOneAction(Product $product)
    {
        return $this->render('product/view.html.twig', ['product' => $product]);
    }

    public function sellProductAction(Request $request, $id)
    {
        /** @var Product $product */
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null)
        {
            $this->redirectToRoute('shop_index');
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute('shop_index');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->uploadPicture($product);
            $product->setAvailable(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('product/sell.html.twig',
            ['product' => $product, 'form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @Route("/category/{id}", name="view_category")
     *
     */
    public function viewCategory($id, Request $request)
    {
        $paginator = $this->get('knp_paginator');
        /** @var SortProducts $sort_products */
        $sort = $this->get('sort_products');
        $sort = $sort->sort($request);

        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)->findAllByCategory($id)->orderBy($sort[0], $sort[1]),

            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );

        return $this->render("product/all.html.twig", ['products' => $products, ]);
    }

    /**
     * @Route("/products/edit/{id}", name="product_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($id);

        if (is_null($product))
        {
            return $this->redirectToRoute('shop_index');
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin())
        {
            return $this->redirectToRoute('shop_index');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $uploadService = $this->get('picture_upload');
            $uploadService->uploadPicture($product);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig',
            ['product' => $product, 'form' => $form->createView(), 'id' => $product->getId()]);
    }

    /**
     * @Method("POST")
     * @Route("products/delete/{id}", name="product_delete")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Product $product)
    {
        if ($product === null) {
            $this->addFlash('error', "Product doesn't exist!");
            return $this->redirectToRoute("products_all");
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isOwner($product) && !$currentUser->isAdmin())
        {
            $this->addFlash('error', "You don't have authorization to delete this product!");
            return $this->redirectToRoute("products_all");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        $this->addFlash('notice', "You deleted " . $product->getName() . " successfully!");

        return $this->redirectToRoute('user_inventory');
    }


    /**
     * @Route("products/sell/{id}", name="sell_product")
     */
    public function sellProduct()
    {

    }
}