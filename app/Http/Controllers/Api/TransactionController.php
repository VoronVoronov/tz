<?php
declare(strict_types=1);

/**
 * Контроллер API для работы с транзакциями
 *
 * @category Controllers
 * @package  App\Http\Controllers\Api
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * Класс контроллера транзакций
 *
 * Обрабатывает API-эндпоинты, связанные с транзакциями
 */
final class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {
    }

    /**
     * Список всех транзакций с опциональным поиском
     *
     * @param Request $request Объект запроса
     * 
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $transactions = $this->transactionService->getUserTransactions(
            Auth::user(),
            $request->only(['search'])
        );

        return TransactionResource::collection($transactions);
    }

    /**
     * Получить последние транзакции
     *
     * @param Request $request Объект запроса
     * 
     * @return AnonymousResourceCollection
     */
    public function recent(Request $request): AnonymousResourceCollection
    {
        $transactions = $this->transactionService->getRecentTransactions($request->user());

        return TransactionResource::collection($transactions);
    }
}
