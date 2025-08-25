<?php

namespace App\Http\Controllers\Orders\Resources;

use App\Http\Controllers\Orders\DTOs\OrderDTO;
use App\Http\Controllers\Orders\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     title="Order Resource",
 *     description="Single Order resource",
 *     @OA\Property(property="orderId", type="integer", example=1),
 *     @OA\Property(property="orderBrand", type="string", example="Apple"),
 *     @OA\Property(property="orderDate", type="string", format="date", example="2023-01-01"),
 *     @OA\Property(property="orderPrice", type="number", format="float", example=1500.50),
 *     @OA\Property(
 *         property="orderStatus",
 *         type="object",
 *         @OA\Property(property="value", type="integer", example=1),
 *         @OA\Property(property="label", type="string", example="Approved")
 *     ),
 *     @OA\Property(
 *         property="orderStatusOld",
 *         type="object",
 *         @OA\Property(property="value", type="integer", example=2),
 *         @OA\Property(property="label", type="string", example="Pending")
 *     ),
 *     @OA\Property(property="orderNote", type="string", format="string", example="Test Test"),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2023-01-01 00:00:00"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time", example="2023-01-01 00:00:00")
 * )
 */
class OrderResource extends JsonResource
{
    /**
     * @var OrderDTO|null
     */
    private ?OrderDTO $orderDTO = null;


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'orderId' => $this->id,
            'orderBrand' => $this->brand,
            'orderDate' => $this->date?->format('Y-m-d'),
            'orderPrice' => $this->price,
            'orderStatus' => $this->parseStatus($this->status, OrderStatus::Order),
            'orderStatusOld' => $this->parseStatus($this->status_old),
            'orderNote' => $this->note,
            'orderCreatedAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'orderUpdatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function parseStatus(mixed $status, ?OrderStatus $default = null): ?array
    {
        $status = match (true) {
            $status instanceof OrderStatus => $status,
            is_string($status), is_int($status) => OrderStatus::tryFrom($status),
            default => $default
        };

        return $status ? ['value' => $status->value, 'label' => $status->label()] : null;
    }

    /**
     * @return OrderDTO|null
     */
    public function getOrderDTO(): ?OrderDTO
    {
        return $this->orderDTO;
    }

    /**
     * @param OrderDTO|null $orderDTO
     */
    public function setOrderDTO(?OrderDTO $orderDTO): void
    {
        $this->orderDTO = $orderDTO;
    }
}