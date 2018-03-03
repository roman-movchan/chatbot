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
use pimax\Menu\MenuItem;
use pimax\Menu\LocalizedMenu;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\StructuredMessage;
use pimax\Messages\MessageElement;
use pimax\Messages\MessageReceiptElement;
use pimax\Messages\Address;
use pimax\Messages\Summary;
use pimax\Messages\Adjustment;
use pimax\Messages\AccountLink;
use pimax\Messages\ImageMessage;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;
use pimax\Messages\SenderAction;

class FacebookMessengerController extends Controller
{

    /**
     * @Route("/fbbot", name="fbbot")
     * @Method({"POST", "GET"})
     *
     * @param Request $request
     * @param LoggerInterface $logger
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

        $logger->info('request', [$data]);

        if (!empty($data['entry'][0]['messaging'])) {
            foreach ($data['entry'][0]['messaging'] as $message) {
                try {
                    if (!empty($message['delivery'])) {
                        continue;
                    }

                    if (isset($message['message']['is_echo']) && ($message['message']['is_echo'] == "true")) {
                        continue;
                    }

                    $previousMessage = $this->getDoctrine()->getRepository(FbMessengerMessage::class)
                        ->findOneBy(['senderId' => $message['recipient']['id']], ['created' => 'DESC']);

                    $command = $previousMessage ? $previousMessage->getType() : 'welcome';

//                    if (!empty($message['message'])) {
//                        $command = $message['message']['text'];
//                    } else if (!empty($message['postback'])) {
//                        $command = $message['postback']['payload'];
//                    }

                    $user =  $this->getDoctrine()
                        ->getRepository(FbMessengerUser::class)
                        ->findOneBy(['messengerId' => $message['sender']['id']]);

                    if(!$user) {
                        /** @var UserProfile $userProfile */
                        $userProfile = $bot->userProfile($message['sender']['id']);
                        $user = new FbMessengerUser($message['sender']['id'], $userProfile->getFirstName() , $userProfile->getLastName() );
                        $em->persist($user);
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

                        case 'text':
                            $bot->send(new Message($message['sender']['id'], 'This is a simple text message.'));
                            break;

                        case 'welcome':
                            $bot->send(new Message($message['sender']['id'], 'Hi, this is credit chatbot. Write the desired amount of credit'));
                            $sentMessage = new FbMessengerMessage();
                            $sentMessage->setType('amount');
                            $sentMessage->setText('Hi, this is credit chatbot. Write the desired amount of credit');
                            $sentMessage->setSenderId($message['recipient']['id']); //from chatbot
                            $sentMessage->setRecipientId($message['sender']['id']); //user
                            $em->persist($sentMessage);
                            break;
                        case 'amount':
                            if((int)$message['message']['text'] > 0) {
                                $bot->send(new Message($message['sender']['id'], 'And now choose your employment'));
                                $bot->send(new StructuredMessage($message['sender']['id'],
                                    StructuredMessage::TYPE_BUTTON,
                                    [
                                        'text' => 'And now choose your employment',
                                        'buttons' => [
                                            new MessageButton(MessageButton::TYPE_POSTBACK, 'Official'),
                                            new MessageButton(MessageButton::TYPE_POSTBACK, 'Private'),
                                            new MessageButton(MessageButton::TYPE_POSTBACK, 'Pensioner')
                                            //TODO: add more
                                        ]
                                    ]
                                ));

                                $sentMessage = new FbMessengerMessage();
                                $sentMessage->setType('employment');
                                $sentMessage->setText('And now choose your employment');
                                $sentMessage->setSenderId($message['recipient']['id']);
                                $sentMessage->setRecipientId($message['sender']['id']);
                                $em->persist($sentMessage);
                            } else {
                                $bot->send(new Message($message['sender']['id'], 'Amount must be more than 0. Write right amount'));
                            }

                            break;
//                        case 'profile':
//                            $user = $bot->userProfile($message['sender']['id']);
//                            $bot->send(new StructuredMessage($message['sender']['id'],
//                                StructuredMessage::TYPE_GENERIC,
//                                [
//                                    'elements' => [
//                                        new MessageElement($user->getFirstName()." ".$user->getLastName(), " ", $user->getPicture())
//                                    ]
//                                ],
//                                [
//                                    new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button','PAYLOAD')
//                                ]
//                            ));
//                            break;

                        default:
                            $bot->send(new Message($message['sender']['id'], 'Sorry. I don’t understand you. '. $user->getFirstName()));
                    }
                } catch (\Exception $e) {
                    $logger->critical($e->getMessage(), array(
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