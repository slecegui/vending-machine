<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PurchaseControllerTest extends WebTestCase
{
    public function testInsertCoinWithValidDecimals(): void
    {
        $client = static::createClient();

        $mockUseCase = $this->createMock(\App\Application\Purchase\InsertCoinUseCase::class);
        $mockUseCase->method('execute')->willReturn(5);

        $client->getContainer()->set(\App\Application\Purchase\InsertCoinUseCase::class, $mockUseCase);

        $client->request('POST', '/api/coins', [], [], [], json_encode(['coin' => 0.05]));
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testInsertCoinWithInvalidValue(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/coins', [], [], [], json_encode(['coin' => 0.06]));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}
