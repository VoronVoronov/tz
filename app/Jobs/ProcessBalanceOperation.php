<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\Balance\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBalanceOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * @param int $userId ID пользователя
     * @param float $amount Сумма операции
     * @param string $type Тип операции (deposit/withdrawal)
     * @param string $description Описание операции
     */
    public function __construct(
        private readonly int $userId,
        private readonly float $amount,
        private readonly string $type,
        private readonly string $description
    ) {
    }

    /**
     * Выполнить задание.
     */
    public function handle(BalanceService $balanceService): void
    {
        try {
            $user = User::findOrFail($this->userId);

            $action = $this->type === 'withdrawal' ? 'subtract' : 'add';
            $balanceService->changeBalance(
                $user->id,
                $this->amount,
                $action,
                $this->description
            );

            Log::info('Balance operation processed', [
                'user_id' => $this->userId,
                'amount' => $this->amount,
                'type' => $this->type,
                'description' => $this->description
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to process balance operation', [
                'user_id' => $this->userId,
                'amount' => $this->amount,
                'type' => $this->type,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
