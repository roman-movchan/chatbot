<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class FacebookMessengerControllerTest extends WebTestCase
{
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

    /**
     * @dataProvider getRequests
     */
    public function testRequests($requestString)
    {
        $message = json_decode($requestString);

        $client = static::createClient();

        $client->request('GET',
            $client->getContainer()->get('router')->generate('fbbot'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($message)
        );

        $result = $client->getResponse()->getContent();
        $this->assertEquals(JsonResponse::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function getRequests()
    {
        yield ['{"object":"page","entry":[{"id":"155365675175754","time":1519747902081,"messaging":[{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519747902068,"delivery":{"mids":["mid.$cAADXuZMmdtFoCU3w-Fh2AioO-626"],"watermark":1519747901688,"seq":0}}]}]}'];
        yield ['{"object":"page","entry":[{"id":"155365675175754","time":1519747912994,"messaging":[{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519747912846,"message":{"tags":{"source":"customer_chat_plugin"},"mid":"mid.$cAADXuZMmdtFoCU4cjlh2AjUBM2Xp","seq":499368,"text":"text"}}]}]}'];
        yield ['{"object":"page","entry":[{"id":"155365675175754","time":1519747914350,"messaging":[{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519747914336,"delivery":{"mids":["mid.$cAADXuZMmdtFoCU4hLFh2AjYc0xJp"],"watermark":1519747914028,"seq":0}}]}]}'];
        yield ['{"object":"page","entry":[{"id":"155365675175754","time":1519747940915,"messaging":[{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519747940767,"message":{"tags":{"source":"customer_chat_plugin"},"mid":"mid.$cAADXuZMmdtFoCU6Jn1h2AlArVJt2","seq":499370,"text":"welcome"}}]}]}'];
        yield ['{"object":"page","entry":[{"id":"155365675175754","time":1519747942272,"messaging":[{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519747942255,"delivery":{"mids":["mid.$cAADXuZMmdtFoCU6OfFh2AlFeC7BQ"],"watermark":1519747942012,"seq":0}}]}]}'];
        yield ['{"object":"page","entry":[{"id":"155365675175754","time":1519747947553,"messaging":[{"sender":{"id":"1920030624688511"},"recipient":{"id":"155365675175754"},"timestamp":1519747947402,"message":{"tags":{"source":"customer_chat_plugin"},"mid":"mid.$cAADXuZMmdtFoCU6jilh2Ala6aTrA","seq":499374,"text":"button"}}]}]}'];

    }




}