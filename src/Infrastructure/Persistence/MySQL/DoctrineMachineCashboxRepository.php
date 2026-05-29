<?php

namespace App\Infrastructure\Persistence\MySQL;

use App\Domain\Cashbox\CoinDenomination;
use App\Domain\Cashbox\CoinStock;
use App\Domain\Cashbox\MachineCashboxRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CoinStock>
 */
class DoctrineMachineCashboxRepository extends ServiceEntityRepository implements MachineCashboxRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoinStock::class);
    }

    public function addCoins(array $coins): void
    {
        $em = $this->getEntityManager();

        foreach ($coins as $value => $quantity) {
            $denomination = CoinDenomination::tryFrom((int) $value);
            if (!$denomination) {
                throw new \InvalidArgumentException(sprintf('Invalid coin denomination: %d', $value));
            }

            $coinStock = $this->find($denomination);

            if (!$coinStock) {
                $coinStock = new CoinStock($denomination, $quantity);
                $em->persist($coinStock);
            } else {
                $coinStock->addQuantity($quantity);
            }
        }

        $em->flush();
    }
}
