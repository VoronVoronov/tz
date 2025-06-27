<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;

/**
 * Репозиторий пользователей
 */
final class UserRepository implements UserRepositoryInterface
{
    /**
     * Создать пользователя
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Найти пользователя по email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
