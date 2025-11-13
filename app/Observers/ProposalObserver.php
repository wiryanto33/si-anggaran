<?php

namespace App\Observers;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

class ProposalObserver
{
    public function creating(Proposal $p): void
    {
        if (empty($p->kode_usulan)) {
            $uid = Auth::id() ?: 'SYS';
            $p->kode_usulan = 'PR-' . now()->format('Ymd-His') . '-' . $uid;
        }
    }
}
