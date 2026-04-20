<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Models\SaleItem;

class SaleRepository
{
    public function createSale(array $data): Sale
    {
        return Sale::create($data);
    }

    public function createSaleItem(array $data): SaleItem
    {
        return SaleItem::create($data);
    }
}
