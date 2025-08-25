<?php

namespace App\Http\Controllers\Orders\DTOs;

use App\Http\Controllers\Customers\Models\Customer;
use App\Http\Controllers\Orders\Enums\OrderStatus;

readonly class OrderDTO
{
    /**
     * @param Customer $customer
     * @param string $date
     * @param float $price
     * @param string $brand
     * @param OrderStatus $status
     * @param OrderStatus|null $statusOld
     * @param string|null $note
     */
    public function __construct(
        public Customer     $customer,
        public string       $brand,
        public string       $date,
        public float        $price,
        public OrderStatus  $status,
        public ?OrderStatus $statusOld = null,
        public ?string      $note = null
    )
    {
    }

    /**
     * @param array $data
     * @return OrderDTO
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            customer: Customer::findOrFail($data['customerId']),
            brand: $data['orderBrand'],
            date: $data['orderDate'],
            price: (float)$data['orderPrice'],
            status: OrderStatus::fromValue($data['orderStatus']),
            statusOld: OrderStatus::fromValue($data['orderStatusOld'] ?? 1),
            note: $data['orderNote'] ?? null
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer->id,
            'date' => $this->date,
            'price' => $this->price,
            'brand' => $this->brand,
            'status' => $this->status->value,
            'status_old' => $this->statusOld?->value,
            'note' => $this->note,
        ];
    }
}
