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
        return new JsonResponse(['current_credit' => $useCase->execute()]);
    }

    #[Route('/coins', name: 'api_purchase_insert_coin', methods: ['POST'])]
    public function insertCoin(Request $request, InsertCoinUseCase $useCase): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['coin'])) {
            return new JsonResponse(['error' => 'Missing coin value'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $totalCredit = $useCase->execute((int) $data['coin']);
            return new JsonResponse(['current_credit' => $totalCredit]);
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
            return new JsonResponse([
                'status' => 'Success',
                'delivered_product' => $result->productName,
                'change' => $result->changeCoins
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
        return new JsonResponse(['refunded_coins' => $coins]);
    }
}
