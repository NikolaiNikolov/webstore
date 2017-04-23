<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebstoreBundle\Entity\User;

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
     * @Route("/admin")
     */
    public function adminAction()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }
}
