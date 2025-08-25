<?php

namespace App\Http\Controllers\Orders\Filters;

class OrderFilters extends \App\Support\Filters\ApiFilter
{
    protected array $safeParms = [
        'orderId' => ['eq', 'ne'],
        'customerId' => ['eq', 'ne'],
        'orderDate' => ['eq', 'lt', 'lte', 'gt', 'gte', 'ne'],
        'orderPrice' => ['eq', 'lt', 'lte', 'gt', 'gte', 'ne'],
        'orderBrand' => ['lk', 'ilk', 'nlk', 'inlk'],
        'orderStatus' => ['eq', 'ne'],
        'orderStatusOld' => ['eq', 'ne'],
        'orderNote' => ['eq', 'ne'],
    ];
    protected array $columnMap = [
        'orderId' => 'id',
        'customerId' => 'customer_id',
        'orderDate' => 'date',
        'orderPrice' => 'price',
        'orderBrand' => 'brand',
        'orderStatus' => 'status',
        'orderStatusOld' => 'status_old',
        'orderNote' => 'note',
    ];
}