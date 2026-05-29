<?php

namespace App\Infrastructure\Persistence\MySQL;

use App\Domain\Inventory\Product;
use App\Domain\Inventory\ProductRepositoryInterface;
use App\Domain\Inventory\ProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class DoctrineProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByType(ProductType $type): ?Product
    {
        return $this->find($type);
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }
}
