<?php

namespace App\Common;

use App\Models\Procedure;
use App\Models\Expense;
use App\Models\HourlyRate;
use App\Models\PaymentMethod;

class BudgetCalculations
{
    public static function calculateTotalFromNestedItems(
        callable $get,
        string $categoryKey,
        string $itemKey,
        string $modelClass
    ): float {
        $categories = $get($categoryKey) ?? [];

        return collect($categories)->sum(function ($category) use ($itemKey, $modelClass) {
            return collect($category[$itemKey] ?? [])->sum(function ($item) use ($modelClass, $itemKey) {
                $model = $modelClass::find($item["{$itemKey}_id"] ?? null);
                $quantity = $item['quantity'] ?? 0;

                if (! $model) {
                    return 0;
                }

                return match ($model->cost_type) {
                    'PER' => ($model->default_price / 100) * $quantity, // Ex: percentual aplicado sobre algum valor base (ajuste conforme necessário)
                    default => ($model->default_price ?? 0) * $quantity, // Unitário e outros
                };
            });
        });
    }
    public static function calculateBasePrice(callable $get): float
    {
        return self::calculateTotalFromNestedItems($get, 'procedure_category', 'procedures', Procedure::class);
    }

    public static function calculateCostPrice(callable $get): float
    {
        $procedures = self::calculateBasePrice($get);
        $expenses = self::calculateTotalFromNestedItems($get, 'expense_category', 'expenses', Expense::class);

        $hourlyRateId = $get('hourly_rate');
        $hourlyRate = HourlyRate::find($hourlyRateId)?->adjustment_percent ?? 0;


        return $procedures + $expenses + (($procedures + $expenses) * ($hourlyRate / 100));
    }

    public static function calculatePriceWithProfitMargin(callable $get): float
    {
        $subtotal = self::calculateCostPrice($get);
        $profitMargin = $get('profit_margin') ?? 0;

        return $subtotal + ($subtotal * ($profitMargin / 100));
    }

    public static function calculateSalePrice(callable $get): float
    {
        $totalWithProfitMargin = self::calculatePriceWithProfitMargin($get);

        $paymentMethod = $get('payment_method');
        $tax = PaymentMethod::find($paymentMethod)?->adjustment_percent ?? 0;

        return $totalWithProfitMargin + ($totalWithProfitMargin * ($tax / 100));
    }
}
