<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Realization extends Model
{
    protected $fillable = ['proposal_id', 'budget_item_id', 'bendahara_id', 'tanggal', 'nilai_realisasi', 'keterangan', 'bukti_url'];

    protected $casts = ['tanggal' => 'date', 'nilai_realisasi' => 'decimal:2'];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
    public function budgetItem()
    {
        return $this->belongsTo(AnnualBudgetItem::class, 'budget_item_id');
    }
    public function bendahara()
    {
        return $this->belongsTo(User::class, 'bendahara_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
