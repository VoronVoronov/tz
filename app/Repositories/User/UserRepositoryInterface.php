<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;

/**
 * Интерфейс репозитория пользователей
 */
interface UserRepositoryInterface
{
    /**
     * Создать пользователя
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User;

    /**
     * Найти пользователя по email
     */
    public function findByEmail(string $email): ?User;
}
