<?php

namespace App\Http\Controllers\Orders\Repositories;

use App\Http\Controllers\Orders\{DTOs\OrderDTO, Models\Order};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        protected Order $orderModel
    )
    {
    }

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
                                      int   $perPage): LengthAwarePaginator
    {
        $query = $this->orderModel->where($queryItems);

        $maxPerPage = 100;
        $perPage = min($perPage, $maxPerPage);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    /**
     * Create new order
     *
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function create(OrderDTO $orderDTO): Order
    {
        return $this->orderModel->create($orderDTO->toArray());
    }

    /**
     * Update existing order
     *
     * @param int $id
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function update(int $id, OrderDTO $orderDTO): Order
    {
        $order = $this->findOrFail($id);
        $order->update($orderDTO->toArray());

        return $order->fresh();
    }

    /**
     * Find order by ID
     *
     * @param int $id
     * @return Order|string
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id): Order|string
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return "Order not found with ID: {$id}";
        }

        return $order;
    }

    /**
     * Delete order
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $order = $this->findOrFail($id);
        $order->delete();
    }
}