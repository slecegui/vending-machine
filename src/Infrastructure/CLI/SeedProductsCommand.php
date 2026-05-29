<?php

namespace App\Infrastructure\CLI;

use App\Domain\Inventory\Product;
use App\Domain\Inventory\ProductRepositoryInterface;
use App\Domain\Inventory\ProductType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed-products', description: 'Seeds initial products')]
class SeedProductsCommand extends Command
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (ProductType::cases() as $type) {
            $product = $this->productRepository->findByType($type);
            if ($product) {
                $output->writeln(sprintf('<comment>Product %s already exists. skipping.</comment>', $type->value));
                continue;
            }

            $product = new Product($type, 0, $type->getPrice());
            $this->productRepository->save($product);
            $output->writeln(sprintf('<info>Product %s created successfully.</info>', $type->value));
        }

        return Command::SUCCESS;
    }
}
