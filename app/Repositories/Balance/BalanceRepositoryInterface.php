<?php

declare(strict_types=1);

namespace App\Repositories\Balance;

use App\Models\Balance;

/**
 * Интерфейс репозитория балансов
 */
interface BalanceRepositoryInterface
{
    /**
     * Создать баланс
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Balance;

    /**
     * Найти баланс пользователя
     */
    public function findByUserId(int $userId): ?Balance;

    /**
     * Обновить баланс
     */
    public function update(Balance $balance): bool;
}
