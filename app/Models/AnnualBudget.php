<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnualBudget extends Model
{
    protected $fillable = ['tahun', 'satuan_id', 'nomor_dokumen', 'total_rencana', 'status', 'finalized_at'];
    protected $casts = ['finalized_at' => 'datetime', 'total_rencana' => 'decimal:2'];

    public function unit()
    {
        return $this->belongsTo(Satuan::class);
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
    public function items()
    {
        return $this->hasMany(AnnualBudgetItem::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function recalcTotal(): void
    {
        $this->total_rencana = (float) $this->items()
            ->selectRaw('COALESCE(SUM(qty*harga_satuan),0) as t')->value('t');
        $this->saveQuietly();
    }
}
