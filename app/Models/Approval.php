<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Approval extends Model
{
    protected $fillable = ['proposal_id', 'actor_id', 'aksi', 'catatan', 'acted_at'];
    protected $casts = ['acted_at' => 'datetime'];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Approval $approval): void {
            if (empty($approval->actor_id) && Auth::check()) {
                $approval->actor_id = Auth::id();
            }
            if (empty($approval->acted_at)) {
                $approval->acted_at = now();
            }
        });

        static::created(function (Approval $approval): void {
            $proposal = $approval->proposal;
            if (!$proposal) return;

            $aksi = (string) $approval->aksi;
            if (in_array($aksi, ['diajukan', 'diverifikasi', 'disetujui', 'ditolak'], true)) {
                $payload = ['status' => $aksi];
                if ($aksi === 'diajukan' && empty($proposal->tanggal_pengajuan)) {
                    $payload['tanggal_pengajuan'] = now();
                }
                $proposal->forceFill($payload)->saveQuietly();
            }
        });
    }
}
