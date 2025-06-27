<?php

declare(strict_types=1);

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория транзакций
 */
interface TransactionRepositoryInterface
{
    /**
     * Создать транзакцию
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Transaction;

    /**
     * Получить транзакции пользователя с пагинацией
     *
     * @param array<string, mixed> $filters
     */
    public function getUserTransactions(int $userId, array $filters = []): LengthAwarePaginator;
}
