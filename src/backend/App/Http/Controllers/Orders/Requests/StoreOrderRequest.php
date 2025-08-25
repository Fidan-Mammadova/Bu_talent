<?php
declare(strict_types=1);

namespace App\Http\Controllers\Orders\Requests;

use App\Http\Controllers\Orders\DTOs\OrderDTO;
use App\Http\Controllers\Orders\Traits\OrderRequestTrait;
use App\Http\Requests\ApiFormRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="StoreOrderRequest",
 *     title="Store Order Request",
 *     description="Store Order request body data",
 *     type="object",
 *     required={"customerId", "orderId", "orderBrand", "orderDate", "orderPrice", "orderStatus"},
 *     @OA\Property(property="customerId", type="integer", example=123, description="The ID of the customer"),
 *     @OA\Property(property="orderBrand", type="string", example="Nike", description="Brand of the order"),
 *     @OA\Property(property="orderDate", type="string", format="date", example="2024-01-30", description="Date of the order"),
 *     @OA\Property(property="orderPrice", type="number", format="float", example=199.99, description="Total order price"),
 *     @OA\Property(property="orderStatus", type="string", example="Pending", description="Current status of the order")
 * )
 */
class StoreOrderRequest extends ApiFormRequest
{
    use OrderRequestTrait;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = $this->getCommonRules();

        // Add required rules for specific fields
        $requiredFields = [
            'customerId',
            'orderBrand',
            'orderDate',
            'orderPrice',
            'orderStatus',
        ];
        foreach ($requiredFields as $field) {
            if (isset($rules[$field]) && is_array($rules[$field])) {
                array_unshift($rules[$field], 'required');
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return $this->getCommonMessages();
    }

    /**
     * @return OrderDTO
     */
    public function toDTO(): OrderDTO
    {
        return OrderDTO::fromRequest($this->validated());
    }
}