<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Purchase\BuyProductCommand;
use App\Application\Purchase\BuyProductUseCase;
use App\Application\Purchase\GetEscrowCreditUseCase;
use App\Application\Purchase\InsertCoinUseCase;
use App\Application\Purchase\RefundUseCase;
use App\Domain\Cashbox\Exception\NotEnoughChangeException;
use App\Domain\Inventory\Exception\ProductOutOfStockException;
use App\Domain\Sales\Exception\InsufficientFundsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class PurchaseController extends AbstractController
{
    #[Route('/coins', name: 'api_purchase_get_credit', methods: ['GET'])]
    public function getCredit(GetEscrowCreditUseCase $useCase): JsonResponse
    {
        return new JsonResponse(['current_credit' => number_format($useCase->execute() / 100, 2, '.', '')]);
    }

    #[Route('/coins', name: 'api_purchase_insert_coin', methods: ['POST'])]
    public function insertCoin(Request $request, InsertCoinUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['coin']) || !is_numeric($data['coin'])) {
            return new JsonResponse(['error' => 'Missing or invalid coin value'], Response::HTTP_BAD_REQUEST);
        }

        $coinValue = (float) $data['coin'];
        $map = [
            '0.05' => 5,
            '0.10' => 10,
            '0.25' => 25,
            '1.00' => 100,
        ];

        // Usamos number_format para asegurar que la clave sea un string consistente
        $key = number_format($coinValue, 2, '.', '');
        if (!isset($map[$key])) {
            return new JsonResponse(['error' => 'Invalid coin denomination'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $totalCredit = $useCase->execute($map[$key]);
            return new JsonResponse(['current_credit' => number_format($totalCredit / 100, 2, '.', '')]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/buy', name: 'api_purchase_buy', methods: ['POST'])]
    public function buy(Request $request, BuyProductUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['product'])) {
            return new JsonResponse(['error' => 'Missing product selection'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $useCase->execute(new BuyProductCommand((string) $data['product']));
            
            $change = array_map(fn($coin) => number_format($coin / 100, 2, '.', ''), $result->changeCoins);
            
            return new JsonResponse([
                'status' => 'Success',
                'delivered_product' => $result->productName,
                'change' => $change
            ]);
        } catch (InsufficientFundsException | ProductOutOfStockException | NotEnoughChangeException | \InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An unexpected error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/refund', name: 'api_purchase_refund', methods: ['POST'])]
    public function refund(RefundUseCase $useCase): JsonResponse
    {
        $coins = $useCase->execute();
        $refundedCoins = array_map(fn($coin) => number_format($coin / 100, 2, '.', ''), $coins);
        
        return new JsonResponse(['refunded_coins' => $refundedCoins]);
    }
}
