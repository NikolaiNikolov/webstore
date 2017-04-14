<?php

namespace WebstoreBundle\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

            /** @var UploadedFile $file */
            $file = $product->getImage();
            $path = '/../web/images/products/';
            $filename = md5($product->getName() . '' . $product->getAddedOn()->format('Y-m-d H:i:s'));
            $file->move(
                $this->get('kernel')->getRootDir() . $path,
                $filename.'.png');
            $product->setImage('images/products/' . $filename . '.png');
//            $product->setImage('images/products/default.jpg');
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();


            return $this->redirectToRoute('products_all');
        }
        return $this->render('product/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("products", name="products_all")
     */
    public function allProducts()
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findAll();

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
}
