<?php

declare(strict_types=1);

namespace App\DTO\Auth;

use App\Http\Requests\Auth\RegisterRequest;

/**
 * DTO для регистрации пользователя
 */
final class RegisterDTO
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly string $password,
    ) {
    }

    /**
     * Создать DTO из запроса
     */
    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }

    public function getName(): string
    {
        return $this->name;
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
