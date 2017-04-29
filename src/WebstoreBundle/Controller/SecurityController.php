<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use WebstoreBundle\Entity\Role;
use WebstoreBundle\Entity\User;
use WebstoreBundle\Form\UserType;

class SecurityController extends Controller
{
    /**
     * @Route("login", name="security_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'You are already logged in');
            return $this->redirectToRoute('shop_index');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('user/login.html.twig', ['error' => $error]);
    }

    /**
     * @Route("logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("editor", name="a")
     */
    public function editor()
    {

    }

    /**
     * @Route("register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $user->addRole($userRole);
            $user->setImage('images/users/profile-pics/default.jpg');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $this->addFlash('notice', 'You registered successfully!');

            return $this->redirectToRoute('shop_index');
        }
        return $this->render('user/register.html.twig', ['form' => $form->createView()]);
    }

}
