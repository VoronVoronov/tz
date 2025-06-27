<?php

/**
 * Контроллер аутентификации
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\Auth\LoginDTO;
use App\DTO\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

/**
 * Класс контроллера аутентификации
 *
 * Обрабатывает регистрацию и вход пользователей
 */
final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * Регистрация нового пользователя
     *
     * @throws \Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            RegisterDTO::fromRequest($request)
        );

        return response()->json($result, 201);
    }

    /**
     * Вход пользователя
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            LoginDTO::fromRequest($request)
        );

        return response()->json($result);
    }

    /**
     * Выход пользователя
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout(request()->user());

        return response()->json(['message' => 'Выход выполнен успешно']);
    }

    /**
     * Получение текущего пользователя
     */
    public function user(): UserResource
    {
        return new UserResource(request()->user());
    }
}
