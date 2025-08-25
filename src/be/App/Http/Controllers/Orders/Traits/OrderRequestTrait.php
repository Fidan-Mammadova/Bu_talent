<?php
declare(strict_types=1);

namespace App\Http\Controllers\Orders\Traits;

use App\Http\Controllers\Orders\Enums\OrderStatus;
use Illuminate\Validation\Rule;

trait OrderRequestTrait
{
    /**
     * Get common validation rules
     *
     * @return array<string, mixed>
     */
    protected function getCommonRules(): array
    {
        return [
            'customerId' => ['integer', 'exists:customers,id'],
            'orderDate' => ['date_format:Y-m-d'],
            'orderPrice' => ['numeric', 'gte:0'],
            'orderBrand' => ['string'],
            'orderStatus' => ['integer', Rule::in(OrderStatus::values())],
            'orderStatusOld' => ['sometimes', 'nullable', 'integer', Rule::in(OrderStatus::values())],
            'orderNote' => ['sometimes'],
        ];
    }

    /**
     * Get common validation messages
     *
     * @return array<string, string>
     */
    protected function getCommonMessages(): array
    {
        $statusLabels = collect(OrderStatus::cases())->map(fn($case) => "{$case->value} - {$case->label()}")->join(', ');
        return [
            'customerId.required' => 'Müştəri nömrəsi qeyd edilməyib.',
            'customerId.exists' => 'Müştəri nömrəsi düzgün qeyd edilməyib.',
            'orderDate.required' => 'Sifarin tarixi il-ay-gün (2024-01-01) formatına uyğun deyil.',
            'orderPrice.required' => 'Sifarişin qiyməti qeyd edilməyib.',
            'orderPrice.numeric' => 'Sifarişin qiyməti rəqəmlə qeyd edilməyib.',
            'orderBrand.required' => 'Sifarişin Brendi qeyd edilməyib.',
            'orderStatus.required' => 'Sifarişin statusu qeyd edilməlidir.',
            'orderStatus.integer' => 'Sifarişin statusu yalnızca aşağıdakılardan biri ola bilər: ' . implode(', ', OrderStatus::values()),
            'orderStatus.in' => 'Sifarişin statusu yalnızca aşağıdakılardan biri ola bilər: ' . $statusLabels,
            'orderStatusOld.integer' => 'Sifarişin statusu yalnızca aşağıdakılardan biri ola bilər: ' . implode(', ', OrderStatus::values()),
            'orderStatusOld.in' => 'Sifarişin statusu yalnızca aşağıdakılardan biri ola bilər: ' . $statusLabels,
        ];
    }

    /**
     * Frontend'dən gələn camelCase formatını snake_case formatına çeviririk
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_id' => $this->customerId,
            'brand' => $this->orderBrand,
            'date' => $this->orderDate,
            'price' => $this->orderPrice,
            'status' => $this->orderStatus,
            'status_old' => $this->orderStatusOld ?? 1,
            'note' => $this->orderNote,
        ]);
    }
}