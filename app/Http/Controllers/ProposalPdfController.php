<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ProposalPdfController extends Controller
{
    public function download(Proposal $proposal)
    {
        // Eager load relations used in the template
        $proposal->load(['satuan', 'perencana', 'items']);

        // Only allow when proposal is approved
        if ($proposal->status !== 'disetujui') {
            abort(403, 'PDF only available for approved proposals.');
        }

        // Mirror visibility rules from listing
        $user = Auth::user();
        if ($user && $user->hasAnyRole(['Perencana', 'perencana'])) {
            if ($proposal->perencana_id !== $user->id) {
                abort(403);
            }
        }

        $pdf = Pdf::loadView('pdf.proposal-approved', [
            'proposal' => $proposal,
            'printedAt' => now(),
        ])->setPaper('a4');

        $filename = 'Proposal-' . ($proposal->kode_usulan ?? $proposal->id) . '-approved.pdf';
        return $pdf->stream($filename);
    }

    public function downloadBatch(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasAnyRole(['Super Admin', 'Verifikator', 'verifikator'])) {
            abort(403);
        }

        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->filter(fn($v) => is_numeric($v))
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            abort(400, 'Tidak ada ID proposal yang dipilih.');
        }

        $proposals = Proposal::with(['satuan', 'perencana', 'items'])
            ->whereIn('id', $ids)
            ->where('status', 'disetujui')
            ->orderBy('id')
            ->get();

        if ($proposals->isEmpty()) {
            abort(404, 'Tidak ada proposal yang disetujui untuk diunduh.');
        }

        $pdf = Pdf::loadView('pdf.proposal-approved-batch', [
            'proposals' => $proposals,
            'printedAt' => now(),
        ])->setPaper('a4');

        $filename = 'Proposals-approved-bundle.pdf';
        return $pdf->stream($filename);
    }
}
