<?php

namespace App\Http\Controllers\Orders\Services;

use App\Http\Controllers\Orders\DTOs\OrderDTO;
use App\Http\Controllers\Orders\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderServiceInterface
{
    /**
     * @param Request $request
     * @param bool $includeTags
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrders(Request $request,
                              bool    $includeTags,
                              int     $perPage): LengthAwarePaginator;

    /**
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function createOrder(OrderDTO $orderDTO): Order;

    /**
     * @param int $id
     * @return Order|string
     */
    public function getOrderById(int $id): Order|string;

    /**
     * @param int $id
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function updateOrder(int $id, OrderDTO $orderDTO): Order;

    /**
     * @param int $id
     * @return void
     */
    public function deleteOrder(int $id): void;

}