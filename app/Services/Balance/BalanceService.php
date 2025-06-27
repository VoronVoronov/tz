<?php

declare(strict_types=1);

namespace App\Services\Balance;

use App\Models\Balance;
use App\Repositories\Balance\BalanceRepository;
use App\Repositories\Transaction\TransactionRepository;
use Illuminate\Support\Facades\DB;

/**
 * Сервис для работы с балансом
 */
final class BalanceService
{
    public function __construct(
        private readonly BalanceRepository $balanceRepository,
        private readonly TransactionRepository $transactionRepository,
    ) {
    }

    /**
     * Создать начальный баланс для пользователя
     */
    public function createInitialBalance(int $userId): Balance
    {
        return $this->balanceRepository->create([
            'user_id' => $userId,
            'amount' => 0,
        ]);
    }

    /**
     * Получить баланс пользователя
     */
    public function getUserBalance(int $userId): Balance
    {
        return $this->balanceRepository->findByUserId($userId) ?? throw new \Exception('Баланс не найден');
    }

    /**
     * Изменить баланс пользователя
     *
     * @throws \Exception
     */
    public function changeBalance(
        int $userId,
        float $amount,
        string $action,
        string $description
    ): Balance {
        try {
            DB::beginTransaction();

            $balance = $this->getUserBalance($userId);
            $newAmount = $action === 'add' ? 
                $balance->amount + $amount : 
                $balance->amount - $amount;

            if ($newAmount < 0) {
                throw new \Exception('Недостаточно средств');
            }

            $balance->amount = $newAmount;
            $this->balanceRepository->update($balance);

            $this->transactionRepository->create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $action === 'add' ? 'deposit' : 'withdrawal',
                'description' => $description,
            ]);

            DB::commit();

            return $balance;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
