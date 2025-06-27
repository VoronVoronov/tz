<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Сервис для работы с транзакциями
 */
final class TransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
    ) {
    }

    /**
     * Получить все транзакции пользователя
     *
     * @param array<string, mixed> $filters
     */
    public function getUserTransactions(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->transactionRepository->getUserTransactions($user->id, $filters);
    }

    /**
     * Получить последние транзакции пользователя
     */
    public function getRecentTransactions(User $user, int $limit = 5): array
    {
        $paginator = $this->transactionRepository->getUserTransactions($user->id, [], $limit);
        return $paginator->items();
    }
}
