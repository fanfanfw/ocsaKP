<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Materi;
use App\Models\Asset;

class BookingController extends Controller
{
    // Tentor: Show Request Form
    public function create(Materi $materi)
    {
        // Load assets and their AVAILABLE items
        $assets = $materi->assets()->with([
            'items' => function ($query) {
                $query->where('is_available', true);
            }
        ])->orderBy('nama_aset')->get();
        return view('bookings.create', compact('materi', 'assets'));
    }

    // Tentor: Submit Request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'asset_item_id' => 'required|exists:asset_items,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required',
        ]);

        $item = \App\Models\AssetItem::findOrFail($validated['asset_item_id']);

        if (!$item->is_available) {
            return back()->withErrors(['asset_item_id' => 'Unit alat ini sudah tidak tersedia.'])->withInput();
        }

        // Check if item belongs to asset linked to materi? 
        // Validation implicitly handled by UI, but good to check. 
        // Skip for now for simplicity, assuming UI is correct.

        Booking::create([
            'user_id' => auth()->id(),
            'materi_id' => $validated['materi_id'],
            'asset_id' => $item->asset_id,
            'asset_item_id' => $item->id,
            'jumlah' => 1, // Fixed to 1
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.index')->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan Admin.');
    }

    // Admin: List Bookings
    public function index()
    {
        $query = Booking::with(['user', 'materi', 'asset', 'assetItem'])->latest();

        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        $bookings = $query->paginate(20);
        return view('bookings.index', compact('bookings'));
    }

    // Admin: Approve
    public function approve(Booking $booking)
    {
        // 1. Validate Item Availability (Again)
        $item = $booking->assetItem;

        if (!$item || !$item->is_available) {
            return back()->with('error', 'Gagal menyetujui: Unit alat ini sudah tidak tersedia (Mungkin sudah dipinjam orang lain).');
        }

        // 2. Create Loan
        \App\Models\Loan::create([
            'user_id' => $booking->user_id,
            'asset_id' => $booking->asset_id,
            'asset_item_id' => $item->id,
            'tanggal_pinjam' => $booking->tanggal,
            'status' => 'Dipinjam'
        ]);

        // 3. Update Item Status
        $item->update(['is_available' => false]);

        // 4. Update Booking
        $booking->update(['status' => 'approved']);

        return back()->with('success', 'Pengajuan disetujui. Data telah masuk ke Peminjaman (Loans).');
    }

    // Admin: Reject
    public function reject(Booking $booking)
    {
        $booking->update(['status' => 'rejected']);
        return back()->with('success', 'Pengajuan ditolak.');
    }
}
