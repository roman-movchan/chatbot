<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    public function testEmptyLead()
    {
        $client = static::createClient();

        $messge = [
            'object' => 'page',
            'entry' => [
                'id' => 376882919389395,
                'time' => 1458692752478,
                'messaging' => [
                    'sender' => []
    ]
            ]
        ];
        $client->request('POST',
            $client->getContainer()->get('router')->generate('fbbot'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{ "partnerId":"1",  "firstName":"test",  "lastName":"test",  "middleName":"test",  "phone":"+380969182351",  "email":"test@gmail.com",  "amount":"123123",  "employment":"official",  "city":"Енакиево",  "aim":"untilSalary",  "birthDate":"01-04-1990",  "channel": "test",  "productTypes": {          "cardCredit":[6,195,210,117,103,209,211]  },  "call_center": "true"}'
        );

        $result = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"error","message":"Need phone or email","errors":[{"field":"phone","message":"need phone or email"},{"field":"email","message":"Need phone or email"}]}',
            $client->getResponse()->getContent());
    }
}