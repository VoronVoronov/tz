<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Balance\BalanceRepository;
use App\Repositories\Balance\BalanceRepositoryInterface;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Auth\AuthService;
use App\Services\Balance\BalanceService;
use Illuminate\Support\ServiceProvider;

/**
 * Провайдер для регистрации сервисов
 */
final class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Регистрация сервисов и репозиториев
     */
    public function register(): void
    {
        // Репозитории
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BalanceRepositoryInterface::class, BalanceRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);

        // Сервисы
        $this->app->singleton(BalanceService::class);
        $this->app->singleton(AuthService::class);
    }
}
