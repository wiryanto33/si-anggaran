<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'satuan_id',
        'perencana_id',
        'kode_usulan',
        'judul',
        'deskripsi',
        'tahun',
        'tanggal_pengajuan',
        'status',
        'catatan_verifikator',
        'catatan_pimpinan',
        'total_rencana'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'total_rencana' => 'decimal:2',
    ];

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
    public function perencana()
    {
        return $this->belongsTo(User::class, 'perencana_id');
    }
    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class);
    }
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopeForSatuan($q, $satuanId)
    {
        return $q->where('satuan_id', $satuanId);
    }

    public function recalcTotal(): void
    {
        $this->total_rencana = (float) $this->items()
            ->selectRaw('COALESCE(SUM(qty*harga_satuan),0) as t')->value('t');
        $this->saveQuietly();
    }
}
