<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalItem extends Model
{
    protected $fillable = [
        'proposal_id',
        'uraian',
        'qty',
        'satuan',
        'harga_satuan',
        'subtotal'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
