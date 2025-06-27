<?php

declare(strict_types=1);

namespace App\Repositories\Balance;

use App\Models\Balance;

/**
 * Репозиторий балансов
 */
final class BalanceRepository implements BalanceRepositoryInterface
{
    /**
     * Создать баланс
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Balance
    {
        return Balance::create($data);
    }

    /**
     * Найти баланс пользователя
     */
    public function findByUserId(int $userId): ?Balance
    {
        return Balance::where('user_id', $userId)->first();
    }

    /**
     * Обновить баланс
     */
    public function update(Balance $balance): bool
    {
        return $balance->save();
    }
}
