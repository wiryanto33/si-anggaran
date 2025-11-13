<?php

namespace App\Observers;

use App\Models\ProposalItem;

class ProposalItemObserver
{
    public function saving(ProposalItem $item): void
    {
        $item->subtotal = (float) $item->qty * (float) $item->harga_satuan;
    }

    public function saved(ProposalItem $item): void
    {
        optional($item->proposal)->recalcTotal();
    }

    public function deleted(ProposalItem $item): void
    {
        optional($item->proposal)->recalcTotal();
    }
}
