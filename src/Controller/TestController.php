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
     * @param Request $request
     */
    public function fbbotAction(Request $request)
    {
        $verify_token = "token"; // Verify token
        if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verify_token) {
            echo $_REQUEST['hub_challenge'];
        }
    }
}