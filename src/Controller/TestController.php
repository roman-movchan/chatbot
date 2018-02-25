<?php

namespace App\Controller;

use pimax\UserProfile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function fbMessageAction(Request $request) : Response
    {
        $verify_token = "token"; // Verify token
        $token = "EAAFWxeSIONMBABATvkfn4XYbVA8PMKdix16CVvB0fuPLxZA8rbAMZAc2bATEVqnYvF15f5ujBo5roOJRWVTAn56iofZCCH20cJMRF82RwNNkbSIYtzMF4tKFZCipjdbhhTPltqe3kI0IeNL3YlZBNRH01KvW3V7M5RQ5IlspV0fxXS9ZCer9l6"; // Page token

        $bot = new FbBotApp($token);

        if ($request->request->has('hub_mode')
            && $request->request->get('hub_mode') === 'subscribe'
            && $request->request->get('hub_verify_token') == $verify_token) {
            return new Response((string)$request->request->get('hub_challenge'));
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data['entry'][0]['messaging']))
        {
            foreach ($data['entry'][0]['messaging'] as $message)
            {
                // Skipping delivery messages
                if (!empty($message['delivery'])) {
                    continue;
                }
//                    // skip the echo of my own messages
                if (isset($message['message']['is_echo']) && ($message['message']['is_echo'] == "true")) {
                    continue;
                }

                $command = "";
                if (!empty($message['message'])) {
                    $command = $message['message']['text'];
                    // ИЛИ Зафиксирован переход по кнопке, записываем как команду
                } else if (!empty($message['postback'])) {
                    $command = $message['postback']['payload'];
                }

// Обрабатываем команду
                switch ($command) {

                    // When bot receive "text"
                    case 'text':
                        $bot->send(new Message($message['sender']['id'], 'This is a simple text message.'));
                        break;

                    // When bot receive "button"
                    case 'button':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_BUTTON,
                            [
                                'text' => 'Choose category',
                                'buttons' => [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Second button'),
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Third button')
                                ]
                            ]
                        ));
                        break;

                    // When bot receive "generic"
                    case 'generic':

                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_GENERIC,
                            [
                                'elements' => [
                                    new MessageElement("First item", "Item description", "", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                        new MessageButton(MessageButton::TYPE_WEB, 'Web link', 'http://facebook.com')
                                    ]),

                                    new MessageElement("Second item", "Item description", "", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'Second button')
                                    ]),

                                    new MessageElement("Third item", "Item description", "", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'Second button')
                                    ])
                                ]
                            ]
                        ));

                        break;

                    // When bot receive "receipt"
                    case 'receipt':

                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_RECEIPT,
                            [
                                'recipient_name' => 'Fox Brown',
                                'order_number' => rand(10000, 99999),
                                'currency' => 'USD',
                                'payment_method' => 'VISA',
                                'order_url' => 'http://facebook.com',
                                'timestamp' => time(),
                                'elements' => [
                                    new MessageReceiptElement("First item", "Item description", "", 1, 300, "USD"),
                                    new MessageReceiptElement("Second item", "Item description", "", 2, 200, "USD"),
                                    new MessageReceiptElement("Third item", "Item description", "", 3, 1800, "USD"),
                                ],
                                'address' => new Address([
                                    'country' => 'US',
                                    'state' => 'CA',
                                    'postal_code' => 94025,
                                    'city' => 'Menlo Park',
                                    'street_1' => '1 Hacker Way',
                                    'street_2' => ''
                                ]),
                                'summary' => new Summary([
                                    'subtotal' => 2300,
                                    'shipping_cost' => 150,
                                    'total_tax' => 50,
                                    'total_cost' => 2500,
                                ]),
                                'adjustments' => [
                                    new Adjustment([
                                        'name' => 'New Customer Discount',
                                        'amount' => 20
                                    ]),

                                    new Adjustment([
                                        'name' => '$10 Off Coupon',
                                        'amount' => 10
                                    ])
                                ]
                            ]
                        ));

                        break;

                    // Other message received
                    default:
                        /** @var UserProfile $user */
                        $user = $bot->userProfile($message['sender']['id']);
                        $bot->send(new Message($message['sender']['id'], 'Sorry. I don’t understand you.'. $user->getFirstName()));
                }

            }
        }

        return new Response();

    }
}