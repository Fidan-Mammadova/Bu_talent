<?php
declare(strict_types=1);

namespace App\Http\Controllers\Orders\Services;

use App\Http\Controllers\Orders\{DTOs\OrderDTO,
    Filters\OrderFilters,
    Models\Order,
    Repositories\OrderRepositoryInterface};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    )
    {
    }

    /**
     * Get filtered orders with pagination
     *
     * @param Request $request
     * @param bool $includeTags
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrders(Request $request, bool $includeTags, int $perPage): LengthAwarePaginator
    {
        $filter = new OrderFilters();
        $queryItems = $filter->transform($request);

        return $this->orderRepository->getFilteredOrders(
            queryItems: $queryItems,
            includeTags: $includeTags,
            perPage: $perPage
        );
    }

    /**
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function createOrder(OrderDTO $orderDTO): Order
    {
        return DB::transaction(function () use ($orderDTO) {
            return $this->orderRepository->create($orderDTO);
        });
    }

    /**
     * @param int $id
     * @return Order|string
     */
    public function getOrderById(int $id): Order|string
    {
        return $this->orderRepository->findOrFail($id);
    }

    /**
     * @param int $id
     * @param OrderDTO $orderDTO
     * @return Order
     */
    public function updateOrder(int $id, OrderDTO $orderDTO): Order
    {
        return DB::transaction(function () use ($id, $orderDTO) {
            return $this->orderRepository->update($id, $orderDTO);
        });
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteOrder(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->orderRepository->delete($id);
        });
    }

}