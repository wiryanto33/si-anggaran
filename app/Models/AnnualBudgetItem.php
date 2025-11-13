<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualBudgetItem extends Model
{
    protected $fillable = [
        'annual_budget_id',
        'sumber_proposal_id',
        'uraian',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(AnnualBudget::class, 'annual_budget_id');
    }

    public function sumberProposal()
    {
        return $this->belongsTo(Proposal::class, 'sumber_proposal_id');
    }
}
