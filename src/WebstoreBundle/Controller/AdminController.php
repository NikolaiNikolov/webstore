<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Form\ProductType;

class AdminController extends Controller
{
    /**
     * @Route("/products/edit/{id}", name="product_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAdmin(Product $product, Request $request)
    {
        $currentUser = $this->getUser();
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($product);

//        if (is_null($product))
//        {
//            return $this->redirectToRoute('shop_index');
//        }
        $oldPic = $product->getImage();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $form->getData();

        if ($form->isSubmitted() && $form->isValid())
        {
            $uploadService = $this->get('picture_upload');
            $uploadService->uploadPicture($product, $oldPic);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig',
            ['product' => $product, 'form' => $form->createView(), 'id' => $product->getId()]);
    }


}
