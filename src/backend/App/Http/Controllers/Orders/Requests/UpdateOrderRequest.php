<?php
declare(strict_types=1);

namespace App\Http\Controllers\Orders\Requests;

use App\Http\Controllers\Orders\DTOs\OrderDTO;
use App\Http\Controllers\Orders\Models\Order;
use App\Http\Controllers\Orders\Traits\OrderRequestTrait;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UpdateOrderRequest",
 *     title="Update Order Request",
 *     description="Update Order request body data",
 *     type="object",
 *     required={"customerId", "orderId", "orderBrand", "orderDate", "orderPrice", "orderStatus"},
 *     @OA\Property(property="customerId", type="integer", example=123, description="The ID of the customer"),
 *     @OA\Property(property="orderBrand", type="string", example="Nike", description="Brand of the order"),
 *     @OA\Property(property="orderDate", type="string", format="date", example="2024-01-30", description="Date of the order"),
 *     @OA\Property(property="orderPrice", type="number", format="float", example=199.99, description="Total order price"),
 *     @OA\Property(property="orderStatus", type="string", example="Pending", description="Current status of the order")
 * )
 */
class UpdateOrderRequest extends ApiFormRequest
{
    use OrderRequestTrait;

    private ?Order $order = null;

    /**
     * Get validation rules
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = $this->getCommonRules();

        try {
            $this->getOrder();
        } catch (ModelNotFoundException $e) {
            return [];
        }

        $requiredFields = [
            'customerId',
            'orderBrand',
            'orderDate',
            'orderPrice',
            'orderStatus',
        ];

        if ($this->method() === 'PUT') {
            // PUT üçün bütün məcburi sahələr yoxlanılır
            foreach ($rules as $field => &$fieldRules) {
                if (!is_array($fieldRules)) {
                    $fieldRules = [$fieldRules];
                }

                if (in_array($field, $requiredFields)) {
                    if (!in_array('required', $fieldRules)) {
                        array_unshift($fieldRules, 'required');
                    }
                } else {
                    if (!in_array('sometimes', $fieldRules)) {
                        array_unshift($fieldRules, 'sometimes');
                    }
                }
            }
        } else {
            // PATCH üçün bütün sahələr optional
            foreach ($rules as $field => &$fieldRules) {
                if (!is_array($fieldRules)) {
                    $fieldRules = [$fieldRules];
                }
                // PATCH-də heç bir field required deyil
                if (!in_array('sometimes', $fieldRules)) {
                    array_unshift($fieldRules, 'sometimes');
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->getCommonMessages();
    }

    /**
     * Convert validated data to DTO
     *
     * @return OrderDTO
     * @throws ModelNotFoundException
     */
    public function toDTO(): OrderDTO
    {
        $validatedData = $this->validated();

        $order = $this->getOrder();

        if ($this->method() === 'PATCH') {
            try {
                // 1. Əvvəlcə bazadan mövcud məlumatları alırıq
                $existingData = [
                    'customerId' => $order->customer_id,
                    'orderBrand' => $order->brand,
                    'orderDate' => $order->date,
                    'orderPrice' => $order->price,
                    'orderStatus' => $order->status,
                    'orderStatusOld' => $order->status_old,
                    'orderNote' => $order->note,
                ];

                // 2. Yeni gələn məlumatları mövcud məlumatlarla birləşdiririk
                // Yalnız dolu gələn sahələr əvəz olunur
                foreach ($validatedData as $key => $value) {
                    if (isset($value)) {
                        $existingData[$key] = $value;
                    }
                }
                $validatedData = $existingData;
            } catch (ModelNotFoundException $e) {
                throw new ModelNotFoundException('Order not found');
            }
        }
        // 3. Köhnə statusu yadda saxlayırıq
        if (isset($validatedData['orderStatus'])) {
            $validatedData['orderStatusOld'] = $order->status;
        }

        return OrderDTO::fromRequest($validatedData);
    }

    /**
     * Get order instance
     *
     * @return Order
     * @throws ModelNotFoundException
     */
    protected function getOrder(): Order
    {
        $orderId = $this->route('internalInvoice')
            ?? $this->route('id')
            ?? $this->input('id');

        if ($orderId === null) {
            throw new ModelNotFoundException("No Order ID provided");
        }

        try {
            $this->order = Order::findOrFail($orderId);
        } catch (ModelNotFoundException $e) {
            throw $e;
        }
        return $this->order;
    }

}