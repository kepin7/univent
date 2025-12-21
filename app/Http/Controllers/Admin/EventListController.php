<?php

namespace App\Http\Controllers\Admin; // Namespace controller admin

use App\Http\Controllers\Controller; // Base controller Laravel
use App\Models\Event; // Model Event
use App\Models\EventRegistration; // Model registrasi event
use Illuminate\Http\Request; // HTTP request
use Illuminate\Support\Facades\Auth; // Autentikasi user
use Illuminate\View\View; // Tipe return View
use Illuminate\Http\RedirectResponse; // Tipe return Redirect
use Illuminate\Support\Facades\DB; // Database & transaksi

class EventListController extends Controller
{
    public function __construct()
    {
        // Middleware auth → user harus login
        $this->middleware('auth');

        // Middleware tambahan untuk cek admin
        $this->middleware(function (Request $request, $next) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user(); // Ambil user yang sedang login

            // Jika user tidak ada atau bukan admin
            if (! $user || ! $user->isAdmin()) {
                abort(403, 'Akses Ditolak. Anda harus menjadi Admin.'); // Tolak akses
            }

            return $next($request); // Lanjutkan request
        });
    }

    // Menampilkan daftar event
    public function index(): View
    {
        $allEvents = Event::orderBy('created_at', 'desc')->get(); // Semua event
        $pendingEvents = Event::where('status', 'pending')->orderBy('created_at', 'desc')->get(); // Event pending
        $approvedEvents = Event::where('status', 'approved')->orderBy('created_at', 'desc')->get(); // Event disetujui
        $rejectedEvents = Event::where('status', 'rejected')->orderBy('created_at', 'desc')->get(); // Event ditolak

        // Kirim data ke view admin.event-list
        return view('admin.event-list', compact(
            'allEvents',
            'pendingEvents',
            'approvedEvents',
            'rejectedEvents'
        ));
    }

    // Menyetujui event
    public function approve(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id); // Ambil event atau 404

        // Transaksi DB agar update konsisten
        DB::transaction(function () use ($event) {
            $event->status = 'approved'; // Ubah status event
            $event->save(); // Simpan perubahan

            // Update semua registrasi terkait
            EventRegistration::where('event_id', $event->id)->update([
                'status' => 'approved',
            ]);
        });

        // Redirect ke halaman list event admin
        return redirect()->route('admin.event-list')
            ->with('success', 'Event ' . $event->event_title . ' berhasil disetujui.');
    }

    // Menolak event
    public function reject(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id); // Ambil event

        // Transaksi DB agar update konsisten
        DB::transaction(function () use ($event) {
            $event->status = 'rejected'; // Ubah status event
            $event->save(); // Simpan perubahan

            // Update semua registrasi terkait
            EventRegistration::where('event_id', $event->id)->update([
                'status' => 'rejected',
            ]);
        });

        // Redirect kembali ke list event
        return redirect()->route('admin.event-list')
            ->with('success', 'Event ' . $event->event_title . ' berhasil ditolak.');
    }

    // Menampilkan detail event untuk admin
    public function show(int $id): View
    {
        // Load event beserta relasi registrations dan user
        $event = Event::with('registrations', 'user')->findOrFail($id);

        return view('admin.event-detail', compact('event')); // Kirim ke view
    }

    // Menghapus event
    public function delete(int $id): RedirectResponse
    {
        $event = Event::findOrFail($id); // Ambil event

        // Hapus event (registrasi terhapus otomatis via cascade)
        $event->delete();

        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }
}