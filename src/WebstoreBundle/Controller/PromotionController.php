<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WebstoreBundle\Entity\Promotion;
use WebstoreBundle\Form\PromotionType;

/**
 * Class PromotionController
 * @package WebstoreBundle\Controller
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class PromotionController extends Controller
{
    /**
     * @Route("promotions/add", name="add_promotion")
     */
    public function addPromotion(Request $request)
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            $this->addFlash('notice', 'Promotion added successfully!');
            return $this->redirectToRoute('add_promotion');
        }
        return $this->render('promotions/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("promotions/all", name="view_promotions")
     */
    public function viewPromotions()
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Promotion::class);
        $promotions = $repo->findAll();

        return $this->render('promotions/all.html.twig', ['promotions' => $promotions]);
    }

    /**
     * @Method("POST")
     * @Route("promotions/delete/{id}/", name="delete_promotion")
     * @param Request $request
     * @param Promotion $promotion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePromotion(Promotion $promotion)
    {
        if ($promotion) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($promotion);
            $em->flush();
            $this->addFlash('notice', 'Promotion deleted successfully!');
        }

        return $this->redirectToRoute('view_promotions');
    }

    /**
     * @param Promotion $promotion
     * @Route("promotions/edit/{id}/", name="edit_promotion")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPromotion(Request $request, Promotion $promotion)
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($promotion)
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($promotion);
                $em->flush();
                $this->addFlash('notice', 'Promotion edited successfully!');
                $this->redirectToRoute('view_promotions');
            }
        }

        return $this->render('promotions/add.html.twig', ['form' => $form->createView()]);
    }
}
