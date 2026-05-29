<?php

namespace App\Application\Maintenance;

readonly class ServiceMachineCommand
{
    /**
     * @param array<string, int> $productsStock Map of ProductType string to stock quantity
     * @param array<int, int> $coins Map of denomination (cents) to quantity
     */
    public function __construct(
        public array $productsStock = [],
        public array $coins = []
    ) {
    }
}
