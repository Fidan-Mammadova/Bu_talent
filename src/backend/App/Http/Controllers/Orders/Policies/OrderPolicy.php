<?php

namespace App\Http\Controllers\Orders\Policies;

use App\Http\Controllers\Orders\Models\Order;
use App\Http\Controllers\User\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function createAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }
}