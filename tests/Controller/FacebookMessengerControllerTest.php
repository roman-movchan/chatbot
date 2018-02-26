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
        $client = static::createClient();

        $client->request('POST',
            $client->getContainer()->get('router')->generate('fbbot'),
            [
                'hub_mode' => 'subscribe',
                'hub_verify_token' => 'token',
                'hub_challenge' => true
            ]
        );

        $result = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $result);
    }

}