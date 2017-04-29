<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebstoreBundle\Entity\Product;
use WebstoreBundle\Entity\User;
use WebstoreBundle\Form\EditUserType;
use WebstoreBundle\Form\UserProductType;

class UserController extends Controller
{
    /**
     * @Route("users/all", name="all_users")
     * @Security("has_role('ROLE_USER')")
     */
    public function allUsersAction()
    {
        $users = $this->getDoctrine()->getManager()->getRepository(User::class)->findAll();

        return $this->render("user/all.html.twig", ['users' => $users]);
    }

    /**
     * @param Request $request
     * @Route("user/products/edit/{id}/", name="user_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function productEdit(Product $product, Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Product::class);
        $product = $repo->find($product);

        if ($product->getOwner() != $this->getUser() && !$this->getUser()->isEditor() && !$this->getUser()->isAdmin()) {
            $this->addFlash('error', 'You don\'t have authorization to edit this product');
            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        $productName = $product->getName();
        $form = $this->createForm(UserProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('notice', "You successfully edited $productName!");
            return $this->redirectToRoute('product_view', ['id' => $product->getId()]);
        }

        return $this->render('product/user_edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("profile", name="view_profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function viewProfile()
    {
        return $this->render('user/profile.html.twig');
    }

    /**
     * @Route("profile/edit", name="profile_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function userEdit(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $repo->find($this->getUser());
        $oldPic = $user->getImage();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $imgPath = 'images/users/profile-pics/';
            $uploadService = $this->get('picture_upload');
            $uploadService->uploadPicture($user, $oldPic, $imgPath);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('notice', 'Profile edited successfully!');
            return $this->redirectToRoute('view_profile');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView()]);
    }
}
