<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\StructuredMessage;
use pimax\Messages\MessageElement;
use pimax\Messages\MessageReceiptElement;
use pimax\Messages\Address;
use pimax\Messages\Summary;
use pimax\Messages\Adjustment;

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
     * @return Response
     */
    public function fbMessageAction(Request $request) : Response
    {
        $verify_token = "token"; // Verify token
        $token = "EAAFWxeSIONMBABATvkfn4XYbVA8PMKdix16CVvB0fuPLxZA8rbAMZAc2bATEVqnYvF15f5ujBo5roOJRWVTAn56iofZCCH20cJMRF82RwNNkbSIYtzMF4tKFZCipjdbhhTPltqe3kI0IeNL3YlZBNRH01KvW3V7M5RQ5IlspV0fxXS9ZCer9l6"; // Page token

        $bot = new FbBotApp($token);

        if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verify_token)
        {
            return new Response($_REQUEST['hub_challenge']);
        } else {

            $data = json_decode(file_get_contents("php://input"), true);
            if (!empty($data['entry'][0]['messaging']))
            {
                foreach ($data['entry'][0]['messaging'] as $message)
                {
                    // Skipping delivery messages
                    if (!empty($message['delivery'])) {
                        continue;
                    }
                    // skip the echo of my own messages
                    if (($message['message']['is_echo'] == "true")) {
                        continue;
                    }

                    $bot->send(new Message($message['sender']['id'], 'Halo'));

                }
            }
        }

        //return new Response();

    }
}