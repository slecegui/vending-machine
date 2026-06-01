<?php

namespace App\Infrastructure\Controller;

use App\Application\Maintenance\ServiceMachineCommand;
use App\Application\Maintenance\ServiceMachineUseCase;
use App\Domain\Cashbox\MachineCashboxRepositoryInterface;
use App\Domain\Inventory\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/service')]
#[IsGranted('ROLE_TECHNICIAN')]
class ServiceController extends AbstractController
{
    public function __construct(
        private readonly ServiceMachineUseCase $useCase,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly MachineCashboxRepositoryInterface $cashboxRepository
    ) {
    }

    #[Route('', name: 'api_service_get_status', methods: ['GET'])]
    public function getStatus(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $coins = $this->cashboxRepository->findAll();

        $productsData = [];
        foreach ($products as $product) {
            $productsData[$product->getType()->value] = [
                'stock' => $product->getStock(),
                'price' => $product->getPrice()
            ];
        }

        $coinsData = [];
        foreach ($coins as $coinStock) {
            $decimalValue = number_format($coinStock->getDenomination()->value / 100, 2, '.', '');
            $coinsData[$decimalValue] = $coinStock->getQuantity();
        }

        return new JsonResponse([
            'products' => $productsData,
            'coins' => $coinsData
        ]);
    }

    #[Route('', name: 'api_service_maintain', methods: ['POST'])]
    public function maintain(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $productsStock = $data['products'] ?? [];
        $coinsInput = $data['coins'] ?? [];

        $coins = [];
        $map = [
            '0.05' => 5,
            '0.10' => 10,
            '0.25' => 25,
            '1.00' => 100,
        ];

        foreach ($coinsInput as $denomination => $quantity) {
            $key = number_format((float) $denomination, 2, '.', '');
            if (!isset($map[$key])) {
                return new JsonResponse(['error' => 'Invalid coin denomination: ' . $denomination], Response::HTTP_BAD_REQUEST);
            }
            $coins[$map[$key]] = (int) $quantity;
        }

        if (empty($productsStock) && empty($coins)) {
            return new JsonResponse(['error' => 'No maintenance data provided'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->useCase->execute(new ServiceMachineCommand($productsStock, $coins));

            return new JsonResponse(['status' => 'Maintenance completed successfully'], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An unexpected error occurred during maintenance'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
