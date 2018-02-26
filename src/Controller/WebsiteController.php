<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;


class WebsiteController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method("GET")
     */
    public function homepageAction() : Response
    {
        return $this->render('default/index.html.twig');
    }
}