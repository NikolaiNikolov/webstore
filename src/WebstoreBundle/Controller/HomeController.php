<?php

namespace WebstoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="cart_index")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
