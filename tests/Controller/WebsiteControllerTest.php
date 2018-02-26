<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebsiteControllerTest extends WebTestCase
{
    public function testWebsiteHomepageSuccess()
    {
        $client = static::createClient();

        $client->request('GET', $client->getContainer()->get('router')->generate('homepage'));

        $client->getResponse()->getContent();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}