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
                                'id' => 111111
                            ],
                            'recipient' => (object) [
                                'id' => 222222
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
        //fbbot?hub.mode=subscribe&hub.challenge=382059564&hub.verify_token=token
        $client = static::createClient();

        $client->request('GET',
            $client->getContainer()->get('router')->generate('fbbot'),
            [
                'hub.mode' => 'subscribe',
                'hub.verify_token' => 'token',
                'hub.challenge' => 382059564
            ]
        );

        $result = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(382059564, $result);
    }

}