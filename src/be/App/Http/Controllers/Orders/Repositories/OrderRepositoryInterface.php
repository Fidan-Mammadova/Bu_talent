<?php

namespace App\Http\Controllers\Orders\Repositories;

use App\Http\Controllers\Orders\{DTOs\OrderDTO, Models\Order};
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * Get filtered orders with pagination
     *
     * @param array $queryItems
     * @param bool $includeTags
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFilteredOrders(array $queryItems,
                                      bool  $includeTags,
                                      int   $perPage): LengthAwarePaginator;

    /**
     * Create new order
     *
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function create(OrderDTO $orderDTO): Order;

    /**
     * Find order by ID
     *
     * @param int $id
     * @return Order|string
     */
    public function findOrFail(int $id): Order|string;

    /**
     * Update existing order
     *
     * @param int $id
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function update(int $id, OrderDTO $orderDTO): Order;

    /**
     * Delete order
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;

}