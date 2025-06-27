<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Services\Balance\BalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Сервис аутентификации
 */
final class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly BalanceService $balanceService,
    ) {
    }

    /**
     * Регистрация нового пользователя
     *
     * @throws \Throwable
     */
    public function register(RegisterDTO $dto): array
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->create([
                'name' => $dto->getName(),
                'email' => $dto->getEmail(),
                'password' => Hash::make($dto->getPassword()),
            ]);

            $this->balanceService->createInitialBalance($user->id);

            $token = $user->createToken('API Token')->accessToken;

            DB::commit();

            return [
                'token' => $token,
                'user' => $user,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Вход пользователя
     *
     * @throws ValidationException
     */
    public function login(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->getEmail());

        if (!$user || !Hash::check($dto->getPassword(), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные'],
            ]);
        }

        $token = $user->createToken('API Token')->accessToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Выход пользователя
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
