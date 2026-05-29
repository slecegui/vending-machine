<?php

namespace App\Tests\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceControllerTest extends WebTestCase
{
    public function testServiceEndpointRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/service', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['coins' => [5 => 10]]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testServiceEndpointSuccess(): void
    {
        $client = static::createClient();

        // 1. Login to get token
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'tech@vending.com',
                'password' => 'tech123',
            ])
        );
        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token'];

        // 2. Request maintenance
        $client->request(
            'POST',
            '/api/service',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ],
            json_encode([
                'products' => ['WATER' => 10],
                'coins' => [25 => 5]
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Maintenance completed successfully', $client->getResponse()->getContent());
    }

    public function testServiceEndpointBadRequest(): void
    {
        $client = static::createClient();

        // 1. Login
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'tech@vending.com', 'password' => 'tech123']));
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        // 2. Request with invalid coin
        $client->request(
            'POST',
            '/api/service',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $token],
            json_encode(['coins' => [7 => 1]])
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Invalid coin denomination: 7', $client->getResponse()->getContent());
    }

    public function testGetServiceStatus(): void
    {
        $client = static::createClient();

        // 1. Login
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'tech@vending.com', 'password' => 'tech123']));
        $token = json_decode($client->getResponse()->getContent(), true)['token'];

        // 2. Get status
        $client->request(
            'GET',
            '/api/service',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertArrayHasKey('products', $data);
        $this->assertArrayHasKey('coins', $data);
        $this->assertArrayHasKey('WATER', $data['products']);
    }
}
