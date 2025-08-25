<?php

namespace App\Support\Services;

use App\Http\Controllers\DIM\Models\DIM;

class CalculateConcessionService
{
    /**
     * Yeni qiymətləri hesablayan metod.
     *
     * @param DIM $dim
     * @return array
     * @throws \JsonException
     */
    public static function calculateValues(DIM $dim): array
    {
        // Əgər request-dən dəyər gəlirsə, onu götür, yoxdursa DIM dəyərini götür
        $factoryInvoicePrice = $dim->factory_invoice_price ?? 0;
        $commissionPercentageOne = $dim->commission_percentage_one ?? 0;
        $commissionPercentageTwo = $dim->commission_percentage_two ?? 0;

        $vmpecAmount = $dim->concession_vmpec_price ?? 0;
        $ibbAmount = $dim->concession_ibb_price ?? 0;
        $luxuryExchangeAmount = $dim->concession_luxury_exchange_amount_price ?? 0;
        $cashCreditNote = $dim->concession_cash_credit_note_price ?? 0;
        $transferCreditNote = $dim->concession_transfer_credit_note_price ?? 0;

        // Hesablamalar
        $X = round($factoryInvoicePrice - $vmpecAmount);
        $Y = round($X * $commissionPercentageOne / 100);
        $Z = round($X * $commissionPercentageTwo / 100);
        $AA = round($X - $Y - $Z);
        $AF = round($AA - $ibbAmount - $luxuryExchangeAmount);
        $yeniAF = round($AA - $ibbAmount - $transferCreditNote - $cashCreditNote - $luxuryExchangeAmount);

        return compact('X', 'Y', 'Z', 'AA', 'AF', 'yeniAF');
    }
}
