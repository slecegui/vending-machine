<?php

namespace App\Tests\Application\Maintenance;

use App\Application\Maintenance\ServiceMachineCommand;
use App\Application\Maintenance\ServiceMachineUseCase;
use App\Domain\Cashbox\MachineCashboxRepositoryInterface;
use App\Domain\Inventory\Product;
use App\Domain\Inventory\ProductRepositoryInterface;
use App\Domain\Inventory\ProductType;
use PHPUnit\Framework\TestCase;

class ServiceMachineUseCaseTest extends TestCase
{
    private ProductRepositoryInterface $productRepository;
    private MachineCashboxRepositoryInterface $cashboxRepository;
    private ServiceMachineUseCase $useCase;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->cashboxRepository = $this->createMock(MachineCashboxRepositoryInterface::class);
        $this->useCase = new ServiceMachineUseCase(
            $this->productRepository,
            $this->cashboxRepository
        );
    }

    public function testTechnicianCanUpdateProductStock(): void
    {
        $water = new Product(ProductType::WATER, 10, 65);
        
        $this->productRepository->expects($this->once())
            ->method('findByType')
            ->with(ProductType::WATER)
            ->willReturn($water);

        $this->productRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Product $product) {
                return $product->getType() === ProductType::WATER && $product->getStock() === 15;
            }));

        $command = new ServiceMachineCommand(
            productsStock: ['WATER' => 5],
            coins: []
        );

        $this->useCase->execute($command);
    }

    public function testTechnicianCanUpdateProductPrice(): void
    {
        $water = new Product(ProductType::WATER, 10, 65);
        
        $this->productRepository->expects($this->once())
            ->method('findByType')
            ->with(ProductType::WATER)
            ->willReturn($water);

        $this->productRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Product $product) {
                return $product->getPrice() === 70;
            }));

        $command = new ServiceMachineCommand(
            productsStock: ['WATER' => ['price' => 70]],
            coins: []
        );

        $this->useCase->execute($command);
    }

    public function testTechnicianCanAddCoinsToCashbox(): void
    {
        $this->cashboxRepository->expects($this->once())
            ->method('addCoins')
            ->with([5 => 10, 10 => 20]);

        $command = new ServiceMachineCommand(
            productsStock: [],
            coins: [5 => 10, 10 => 20]
        );

        $this->useCase->execute($command);
    }

    public function testThrowsExceptionForInvalidCoinDenomination(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid coin denomination: 7');

        $command = new ServiceMachineCommand(
            productsStock: [],
            coins: [7 => 1]
        );

        $this->useCase->execute($command);
    }

    public function testThrowsExceptionForUnknownProduct(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid product type: UNKNOWN');

        $command = new ServiceMachineCommand(
            productsStock: ['UNKNOWN' => 10],
            coins: []
        );

        $this->useCase->execute($command);
    }
}
