<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function test()
    {
        return $this->render('test/index.html.twig');
    }

    /**
     * @Route("/fbbot", name="fbbot")
     *
     * @param Request $request
     * @return  Response
     */
    public function fbbotAction(Request $request) : Response
    {
        $verify_token = "token"; // Verify token
        if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verify_token) {
            return new Response($_REQUEST['hub_challenge']);
        } else {
            return new Response();
        }
    }
}