<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use WebstoreBundle\Entity\User;
use WebstoreBundle\Form\UserType;

class UserController extends Controller
{

    /**
     * @Route("register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'You registered successfully!');

            return $this->redirectToRoute('security_login');
        }
        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("user/view/{name}", name="user_profile")
     */
    public function profileAction()
    {

    }
}
