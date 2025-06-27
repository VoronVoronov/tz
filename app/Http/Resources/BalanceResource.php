<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource для баланса
 *
 * @property-read Balance $resource
 */
class BalanceResource extends JsonResource
{
    /**
     * Преобразовать ресурс в массив
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'amount' => $this->resource->amount,
            'created_at' => $this->resource->created_at->toISOString(),
            'updated_at' => $this->resource->updated_at->toISOString(),
        ];
    }
}
