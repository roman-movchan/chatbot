<?php

namespace App\Controller;

use App\Entity\FbMessengerMessage;
use App\Entity\FbMessengerUser;
use pimax\UserProfile;
use Psr\Log\LoggerInterface;
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

class FacebookMessengerController extends Controller
{

    /**
     * @Route("/fbbot", name="fbbot")
     * @Method({"POST", "GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function fbMessageAction(Request $request, LoggerInterface $logger) : Response
    {
        $verify_token = "token"; // Verify token
        $token = "EAAFWxeSIONMBABATvkfn4XYbVA8PMKdix16CVvB0fuPLxZA8rbAMZAc2bATEVqnYvF15f5ujBo5roOJRWVTAn56iofZCCH20cJMRF82RwNNkbSIYtzMF4tKFZCipjdbhhTPltqe3kI0IeNL3YlZBNRH01KvW3V7M5RQ5IlspV0fxXS9ZCer9l6"; // Page token

        $bot = new FbBotApp($token);

        if ($request->query->has('hub_mode')
            && $request->query->get('hub_mode') === 'subscribe'
            && $request->query->get('hub_verify_token') == $verify_token) {
            return new Response((string)$request->query->get('hub_challenge'));
        }
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);
        if (!empty($data['entry'][0]['messaging'])) {
            foreach ($data['entry'][0]['messaging'] as $message) {
                try {
                    if (!empty($message['delivery'])) {
                        continue;
                    }

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

                    $senderUser =  $this->getDoctrine()
                        ->getRepository(FbMessengerUser::class)
                        ->findOneBy(['messengerId' => $message['sender']['id']]);

                    if(!$senderUser) {
                        /** @var UserProfile $userProfile */
                        $userProfile = $bot->userProfile($message['sender']['id']);
                        try {
                            $firstName = $userProfile->getFirstName() ? $userProfile->getFirstName() : '';
                            $lastName = $userProfile->getLastName() ? $userProfile->getLastName() : '';
                        } catch (\Exception $e) {
                            $firstName = '';
                            $lastName = '';
                        }
                        $senderUser = new FbMessengerUser($message['sender']['id'], $firstName, $lastName);

                        $em->persist($senderUser);
                    }

                    $msg = new FbMessengerMessage();
                    $msg->setType('text');
                    $msg->setSenderId($message['sender']['id']);
                    $msg->setRecipientId($message['recipient']['id']);
                    if(isset($message['message']['text'])) {
                        $msg->setText($message['message']['text']);
                    } else {
                        $msg->setText('welcome');
                    }

                    $em->persist($msg);

                    switch ($command) {

                        // When bot receive "text"
                        case 'text':
                            $bot->send(new Message($message['sender']['id'], 'This is a simple text message.'));
                            break;

                        case 'welcome':
                            $bot->send(new Message($message['sender']['id'], 'Hi, this is credit chatbot. Write the desired amount of credit'));
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

                        case 'profile':
                            $user = $bot->userProfile($message['sender']['id']);
                            $bot->send(new StructuredMessage($message['sender']['id'],
                                StructuredMessage::TYPE_GENERIC,
                                [
                                    'elements' => [
                                        new MessageElement($user->getFirstName()." ".$user->getLastName(), " ", $user->getPicture())
                                    ]
                                ],
                                [
                                    new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button','PAYLOAD')
                                ]
                            ));
                            break;

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

                        default:
                            /** @var UserProfile $user */
                            $bot->send(new Message($message['sender']['id'], 'Sorry. I don’t understand you. '. $senderUser->getFirstName()));
                    }
                } catch (\Exception $e) {
                    $logger->critical('Bad message', array(
                        $message,
                    ));
                    $bot->send(new Message($message['sender']['id'], 'Some error happened.'));
                }

            }
        }

        $em->flush();

        return new Response();

    }
}