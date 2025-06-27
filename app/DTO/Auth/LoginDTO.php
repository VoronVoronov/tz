<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Http\Requests\Auth\LoginRequest;

/**
 * DTO для входа пользователя
 */
final class LoginDTO
{
    public function __construct(
        private readonly string $email,
        private readonly string $password,
    ) {
    }

    /**
     * Создать DTO из запроса
     */
    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
