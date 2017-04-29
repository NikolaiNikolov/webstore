<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Form\EditorProductType;

class EditorController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("editor/products/edit/{id}/", name="editor_edit")
     */
    public function edit(Product $product, Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($product);
        $productName = $product->getName();
        $form = $this->createForm(EditorProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('notice', "You successfully edited $productName!");
            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('product/edit_editor.html.twig', ['form' => $form->createView()]);
    }
}
