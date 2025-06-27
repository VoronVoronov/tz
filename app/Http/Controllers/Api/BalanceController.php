<?php
declare(strict_types=1);

/**
 * Контроллер API для работы с балансом пользователя
 *
 * @category Controllers
 * @package  App\Http\Controllers\Api
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BalanceResource;
use App\Jobs\ProcessBalanceOperation;
use App\Services\Balance\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Класс контроллера баланса
 *
 * Обрабатывает API-эндпоинты, связанные с балансом
 */
final class BalanceController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService,
    ) {
    }

    /**
     * Показывает текущий баланс пользователя
     */
    public function show(Request $request): BalanceResource
    {
        $balance = $this->balanceService->getUserBalance($request->user()->id);

        return new BalanceResource($balance);
    }

    /**
     * Добавить средства на баланс
     */
    public function deposit(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        ProcessBalanceOperation::dispatch(
            $request->user()->id,
            (float) $request->input('amount'),
            'deposit',
            $request->input('description')
        );

        return response()->json([
            'message' => 'Операция поставлена в очередь'
        ]);
    }

    /**
     * Снять средства с баланса
     */
    public function withdraw(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        ProcessBalanceOperation::dispatch(
            $request->user()->id,
            (float) $request->input('amount'),
            'withdrawal',
            $request->input('description')
        );

        return response()->json([
            'message' => 'Операция поставлена в очередь'
        ]);
    }
}
