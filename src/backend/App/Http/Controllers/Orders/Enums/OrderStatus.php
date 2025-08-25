<?php

namespace App\Http\Controllers\Orders\Enums;


use App\Support\Enums\BaseEnumTrait;

enum OrderStatus: int
{
    use BaseEnumTrait;
    case Order = 1;
    case Production = 2;
    case OnTheRoad = 3;
    case Port = 4;
    case InBaku = 5;
    case Delivered = 6;

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Order => 'Sifariş',
            self::Production => 'İstehsal',
            self::OnTheRoad => 'Yolda',
            self::Port => 'Liman',
            self::InBaku => 'Bakıda',
            self::Delivered => 'Təhvil verildi',
        };
    }
}