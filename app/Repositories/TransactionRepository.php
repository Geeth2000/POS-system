<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\TransactionItem;

class TransactionRepository
{
    public function createTransaction(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function createTransactionItem(array $data): TransactionItem
    {
        return TransactionItem::create($data);
    }
}
