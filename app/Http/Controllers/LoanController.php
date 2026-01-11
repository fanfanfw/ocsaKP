<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetStatusLog;
use App\Models\Loan;
use App\Models\User;
use App\Services\ScheduleStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('reports.index');
        }

        $query = Loan::with(['asset', 'user'])->orderByDesc('tanggal_pinjam');

        $query->where('user_id', Auth::id());

        $loans = $query->paginate(15);

        return view('loans.index', [
            'loans' => $loans,
        ]);
    }

    public function create(Request $request)
    {
        $assets = Asset::orderBy('nama_aset')->get();
        $tentor = User::where('role', 'tentor')->orderBy('name')->get();
        $selectedAssetId = $request->input('asset_id');
        $scheduleStatus = app(ScheduleStatusService::class);
        $scheduledIds = $scheduleStatus->currentScheduledAssetIds();
        $activeCounts = Loan::selectRaw('asset_id, COUNT(*) as total')
            ->where('status', 'Dipinjam')
            ->groupBy('asset_id')
            ->pluck('total', 'asset_id');

        return view('loans.create', [
            'assets' => $assets,
            'tentor' => $tentor,
            'materi_list' => \App\Models\Materi::orderBy('nama')->get(),
            'selectedAssetId' => $selectedAssetId,
            'scheduledIds' => $scheduledIds,
            'activeCounts' => $activeCounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'tanggal_pinjam' => ['nullable', 'date'],
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);
        $scheduleStatus = app(ScheduleStatusService::class);
        $scheduledIds = $scheduleStatus->currentScheduledAssetIds();
        $activeLoans = Loan::where('asset_id', $asset->id)
            ->where('status', 'Dipinjam')
            ->count();
        $available = $asset->jumlah - $activeLoans;

        if ($available < 1) {
            return back()->withErrors([
                'asset_id' => 'Aset sedang tidak tersedia untuk dipinjam.',
            ])->withInput();
        }

        $userId = Auth::id();
        if (Auth::user()->role === 'admin' && !empty($validated['user_id'])) {
            $userId = (int) $validated['user_id'];
        }

        Loan::create([
            'user_id' => $userId,
            'asset_id' => $asset->id,
            'tanggal_pinjam' => $validated['tanggal_pinjam'] ?? now(),
            'status' => 'Dipinjam',
        ]);

        $this->syncAssetStatus($asset);

        return redirect()->route('loans.index')->with('success', 'Penggunaan berhasil dicatat.');
    }

    public function returnForm(Loan $loan)
    {
        $this->authorizeLoan($loan);

        return view('loans.return', [
            'loan' => $loan->load('asset'),
        ]);
    }

    public function processReturn(Request $request, Loan $loan)
    {
        $this->authorizeLoan($loan);

        $validated = $request->validate([
            'bukti_kembali' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($loan->status === 'Dikembalikan') {
            return redirect()->route('loans.index')->with('success', 'Penggunaan sudah dikembalikan.');
        }

        $filename = null;
        if ($request->hasFile('bukti_kembali')) {
            $file = $request->file('bukti_kembali');
            $safeName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $safeName .= '.' . $file->getClientOriginalExtension();
            $file->storeAs('bukti_kembali', $safeName, 'public');
            $filename = $safeName;
        }

        $loan->update([
            'status' => 'Dikembalikan',
            'tanggal_kembali' => now(),
            'bukti_kembali' => $filename ?? $loan->bukti_kembali,
        ]);

        if ($loan->asset) {
            $this->syncAssetStatus($loan->asset);
        }

        return redirect()->route('loans.index')->with('success', 'Penggunaan berhasil dikembalikan.');
    }

    private function authorizeLoan(Loan $loan): void
    {
        if (Auth::user()->role === 'admin') {
            return;
        }

        if ($loan->user_id !== Auth::id()) {
            abort(403);
        }
    }

    private function syncAssetStatus(Asset $asset): void
    {
        $activeLoans = Loan::where('asset_id', $asset->id)
            ->where('status', 'Dipinjam')
            ->count();
        $newStatus = $activeLoans >= $asset->jumlah ? 'Dipinjam' : 'Tersedia';

        if ($asset->status !== $newStatus) {
            $asset->update(['status' => $newStatus]);
            AssetStatusLog::create([
                'asset_id' => $asset->id,
                'status' => $newStatus,
                'updated_at' => now(),
            ]);
        }
    }
}
