<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RateControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $client->xmlHttpRequest('GET', '/rate/USD/EUR/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('success', $json);
        $this->assertArrayHasKey('value', $json);
        $this->assertTrue($json['success']);


        $client->xmlHttpRequest('GET', '/rate/USD/TST/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $json = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('success', $json);
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayNotHasKey('value', $json);
        $this->assertFalse($json['success']);
    }
}