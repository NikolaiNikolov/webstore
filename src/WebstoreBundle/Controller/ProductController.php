<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Form\ProductType;

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

<<<<<<< HEAD
            $this->uploadPicture($product);

=======
            /** @var UploadedFile $file */
            $file = $product->getImage();
            $path = '/../web/images/products/';
            $filename = md5($product->getName() . '' . $product->getAddedOn()->format('Y-m-d H:i:s'));
            $file->move(
                $this->get('kernel')->getRootDir() . $path,
                $filename.'.png');

            $product->setImage('images/products/' . $filename . '.png');
//            $product->setImage('images/products/default.jpg');
>>>>>>> f4e175df6c247e5c26c3ea7f4cc72669d236b479
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();


            return $this->redirectToRoute('products_all');
        }
        return $this->render('product/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("products", name="products_all")
     * @param Request $request
     */
    public function allProducts(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $sort = $this->sort($request);
        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)->findAllQuery()->orderBy($sort[0], $sort[1]),
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

    /**
     * @param Request $request
     * @Route("/category/{id}", name="view_category")
     *
     */
    public function viewCategory($id, Request $request)
    {
<<<<<<< HEAD
        $paginator = $this->get('knp_paginator');
        $sort = $this->sort($request);
        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)->findAllByCategory($id)->orderBy($sort[0], $sort[1]),
=======
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT a FROM WebstoreBundle:Product a WHERE a.category = '$id' ORDER BY a.addedOn DESC";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
>>>>>>> f4e175df6c247e5c26c3ea7f4cc72669d236b479
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 6)
        );

<<<<<<< HEAD
        return $this->render("product/all.html.twig", ['products' => $products, ]);
    }

    public function sort(Request $request)
    {
        $filter = $request->get('filter');
        $sort = ['a.id', 'desc'];

        if ($filter)
        {
            switch ($filter) {
                case 1:
                    $sort = ['a.name', 'asc'];
                    break;
                case 2:
                    $sort = ['a.name', 'desc'];
                    break;
                case 3:
                    $sort = ['a.price', 'asc'];
                    break;
                case 4:
                    $sort = ['a.price', 'desc'];
                    break;
                default:
                    break;
            }
        }

        return $sort;
    }

    public function uploadPicture(Product $product)
    {
        /** @var UploadedFile $file */
        $file = $product->getImage();

        if ($file) {
            $path = '/../web/images/products/';
            $filename = md5($product->getName() . '' . $product->getAddedOn()->format('Y-m-d H:i:s'));
            $file->move(
                $this->get('kernel')->getRootDir() . $path,
                $filename.'.png');
            $product->setImage('images/products/' . $filename . '.png');
        } else {
            $product->setImage('images/products/default.png');
        }
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig',
            ['product' => $product, 'form' => $form->createView()]);
=======
        return $this->render("product/all.html.twig", ['products' => $pagination]);
>>>>>>> f4e175df6c247e5c26c3ea7f4cc72669d236b479
    }
}
