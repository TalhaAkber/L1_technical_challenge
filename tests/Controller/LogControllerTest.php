<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogControllerTest extends WebTestCase
{
    public function testCount(): void
    {
        $client = static::createClient();

        $client->request('GET', '/count');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonStringEqualsJsonString(
            $client->getResponse()->getContent(),
            json_encode(['counter' => 5])
        );
    }

    public function testCountWithServiceNameFilter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/count?serviceNames[]=USER-SERVICE');
        $this->assertJsonStringEqualsJsonString(
            $client->getResponse()->getContent(),
            json_encode(['counter' => 4])
        );


        $client->request('GET', '/count?serviceNames[]=INVOICE-SERVICE');
        $this->assertJsonStringEqualsJsonString(
            $client->getResponse()->getContent(),
            json_encode(['counter' => 1])
        );
    }

    public function testCountWithAllFilters(): void
    {
        $client = static::createClient();

        $client->request('GET', '/count?serviceNames[]=USER-SERVICE&startDate=2018-08-11 09:21:54&statusCode=201');
        $this->assertJsonStringEqualsJsonString(
            $client->getResponse()->getContent(),
            json_encode(['counter' => 2])
        );
    }

    public function testBadRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', '/count?startDate=invalid_date');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}