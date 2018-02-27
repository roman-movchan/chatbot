<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FacebookMessengerControllerTest extends WebTestCase
{
    public function testControllerSuccess()
    {
        $client = static::createClient();

        $message = (object) [
            'object' => "page",
            'entry' => [
                (object) [
                    'id' => 376882919389395,
                    'time' => 1458692752478,
                    'messaging' => [
                        (object) [
                            'sender' => (object)[
                                'id' => 100005910641051
                            ],
                            'recipient' => (object) [
                                'id' => 376882919389395
                            ],
                            "timestamp" => 1458692752478,
                            'message' => (object)[
                                "mid" => "mid.1457764197618:41d102a3e1ae206a38",
                                "text" => "hello, world!",
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client->request('POST',
            $client->getContainer()->get('router')->generate('fbbot'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($message)
        );

        json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testWelcomeMessageSuccess()
    {
        $client = static::createClient();

        $message = (object) [
            'object' => "page",
            'entry' => [
                (object) [
                    'id' => 376882919389395,
                    'time' => 1458692752478,
                    'messaging' => [
                        (object) [
                            'sender' => (object)[
                                'id' => 100005910641051
                            ],
                            'recipient' => (object) [
                                'id' => 376882919389395
                            ],
                            "timestamp" => 1458692752478,
                            'postback' => (object)[
                                "payload" => "welcome",
                                "title" => "null",
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client->request('POST',
            $client->getContainer()->get('router')->generate('fbbot'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($message)
        );

        json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testControllerVerify()
    {
        //fbbot?hub.mode=subscribe&hub.challenge=1494994378&hub.verify_token=token
        $client = static::createClient();

        $client->request('GET',
            $client->getContainer()->get('router')->generate('fbbot'),
            [
                'hub_mode' => 'subscribe',
                'hub_verify_token' => 'token',
                'hub_challenge' => 1494994378
            ]
        );

        $result = $client->getResponse()->getContent();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1494994378, $result);
    }

    //todo: test for
    //{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519676310083,"message":{"tags":{"source":"customer_chat_plugin"},"mid":"mid.$cAADXuZMmdtFoBQmKQ1h08RBO4nN3","seq":499118,"sticker_id":369239263222822,"attachments":[{"type":"image","payload":{"url":"https://scontent-dft4-2.xx.fbcdn.net/v/t39.1997-6/851557_369239266556155_759568595_n.png?_nc_ad=z-m&_nc_cid=0&oh=557fb26a3950519bda159df1f43bd7b5&oe=5B1451DC","sticker_id":369239263222822}}]}}]

    //[{"recipient":{"id":"155365675175754"},"timestamp":1519684168995,"sender":{"id":"1920030624688511"},"postback":{"payload":"First button","title":"First button"}}]



}