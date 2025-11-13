<?php

namespace App\Observers;

use App\Models\AnnualBudgetItem;

class AnnualBudgetItemObserver
{
    public function saving(AnnualBudgetItem $item): void
    {
        $item->subtotal = (float) $item->qty * (float) $item->harga_satuan;
    }

    public function saved(AnnualBudgetItem $item): void
    {
        optional($item->budget)->recalcTotal();
    }

    public function deleted(AnnualBudgetItem $item): void
    {
        optional($item->budget)->recalcTotal();
    }
}
