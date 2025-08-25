<?php

namespace App\Http\Controllers\Orders\Models;

use App\Http\Controllers\Customers\Models\Customer;
use App\Http\Controllers\FactoryInvoices\Models\FactoryInvoice;
use App\Http\Controllers\Orders\Enums\OrderStatus;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, Relations\BelongsTo, Relations\HasOne};
use Illuminate\Routing\Route;

/**
 * @method static findOrFail(Route|object|string|null $orderId)
 * @method create(array $toArray)
 * @method where(array $queryItems)
 * @method find(int $id)
 * @property integer $id
 * @property integer $customer_id
 * @property string $brand
 * @property string $date
 * @property float $price
 * @property OrderStatus $status
 * @property OrderStatus $status_old
 * @property string|null $note
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "old_id",
        "customer_id",
        "factory_invoice_id",
        "brand",
        "date",
        "price",
        "status",
        "status_old",
        "note",
    ];
    protected $casts = [
        'date' => 'date:d.m.Y',
        'price' => 'float',
        'status' => OrderStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function factoryInvoices(): HasOne
    {
        return $this->hasOne(FactoryInvoice::class);
    }
}
