<?php

declare(strict_types=1);

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Репозиторий транзакций
 */
final class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * Создать транзакцию
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    /**
     * Получить транзакции пользователя с пагинацией
     *
     * @param array<string, mixed> $filters
     */
    public function getUserTransactions(int $userId, array $filters = [], ?int $limit = null): LengthAwarePaginator
    {
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if (isset($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        $perPage = $limit ?? 10;
        return $query->paginate($perPage);
    }
}
