<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PurchaseControllerChangeTest extends WebTestCase
{
    public function testBuyProductChangeFormatting(): void
    {
        $client = static::createClient();

        // Mocking BuyProductUseCase to return a specific change set (e.g., 100 cents = 1.00 coin)
        $mockResult = new \App\Application\Purchase\PurchaseResultDTO('WATER', [100]);
        $mockUseCase = $this->createMock(\App\Application\Purchase\BuyProductUseCase::class);
        $mockUseCase->method('execute')->willReturn($mockResult);

        $client->getContainer()->set(\App\Application\Purchase\BuyProductUseCase::class, $mockUseCase);

        $client->request('POST', '/api/buy', [], [], [], json_encode(['product' => 'WATER']));
        
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data['change']);
        $this->assertEquals(['1.00'], $data['change']);
    }
}
