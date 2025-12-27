<?php

namespace App\Http\Controllers; // Namespace controller

use App\Models\Event; // Model Event
use App\Models\EventRegistration; // Model pendaftaran event
use Illuminate\Http\Request; // HTTP request
use Illuminate\Support\Facades\Auth; // Autentikasi
use Illuminate\View\View; // Tipe return View
use Illuminate\Http\RedirectResponse; // Tipe return Redirect

class EventController extends Controller // Controller event
{
    public function __construct() // Constructor
    {
        $this->middleware('auth')->only([ // Middleware auth
            'create', // Proteksi create
            'store', // Proteksi store
            'update', // Proteksi update
            'showHistory', // Proteksi history
            'showRegistration', // Proteksi detail registrasi
        ]);
    }

    protected function redirectTo(Request $request): ?string // Redirect default auth
    {
        return $request->expectsJson() // Jika JSON
            ? null // Tidak redirect
            : route('login'); // Redirect login
    }

    public function create(Request $request): View|RedirectResponse // Form submit/edit
    {
        $event = null; // Default event kosong

        if ($request->has('edit')) { // Jika mode edit
            $eventId = $request->query('edit'); // Ambil ID event
            $event = Event::findOrFail($eventId); // Cari event

            $isOwner = EventRegistration::where('event_id', $event->id) // Cek event
                ->where('user_id', Auth::id()) // Cek user
                ->exists(); // Pastikan ada

            if (! $isOwner) { // Jika bukan pemilik
                return redirect()->route('user.event.history') // Redirect history
                    ->with('error', 'Anda tidak berhak mengubah event ini.'); // Pesan error
            }

            if ($event->status !== 'pending') { // Jika bukan pending
                return redirect()->route('user.event.history') // Redirect
                    ->with('error', 'Event yang sudah disetujui tidak dapat diubah.'); // Error
            }
        }

        return view('submit-event', compact('event')); // Tampilkan form
    }

    public function store(Request $request): RedirectResponse // Simpan event
    {
        $request->validate([ // Validasi input
            'event_title' => 'required|string|max:255', // Judul
            'organizer_name' => 'required|string|max:255', // Organizer
            'organizer_type' => 'required|string', // Tipe
            'event_category' => 'required|string', // Kategori
            'event_description' => 'required|string', // Deskripsi
            'start_date' => 'required|date', // Tanggal mulai
            'start_time' => 'required', // Jam mulai
            'end_date' => 'required|date|after_or_equal:start_date', // Tanggal akhir
            'end_time' => 'required', // Jam akhir
            'event_location' => 'required|string', // Lokasi
            'registration_link' => 'nullable|url', // Link
            'contact_person' => 'required|string', // Kontak
            'event_poster' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // Poster
        ]);

        $posterData = null; // Default poster kosong

        if ($request->hasFile('event_poster')) { // Jika upload poster
            $file = $request->file('event_poster'); // Ambil file
            if ($file && $file->getRealPath()) { // Cek file valid
                $content = file_get_contents($file->getRealPath()); // Ambil isi
                if ($content !== false) { // Jika berhasil
                    $posterData = base64_encode($content); // Encode base64
                }
            }
        }

        $event = Event::create([ // Simpan event
            'user_id' => Auth::id(), // User login
            'event_title' => $request->event_title, // Judul
            'organizer_name' => $request->organizer_name, // Organizer
            'organizer_type' => $request->organizer_type, // Tipe
            'event_category' => $request->event_category, // Kategori
            'event_description' => $request->event_description, // Deskripsi
            'start_date' => $request->start_date, // Tanggal mulai
            'start_time' => $request->start_time, // Jam mulai
            'end_date' => $request->end_date, // Tanggal akhir
            'end_time' => $request->end_time, // Jam akhir
            'event_location' => $request->event_location, // Lokasi
            'registration_link' => $request->registration_link, // Link
            'contact_person' => $request->contact_person, // Kontak
            'event_poster' => $posterData, // Poster base64
            'status' => 'pending', // Status pending
        ]);

        EventRegistration::create([ // Daftarkan user
            'user_id' => Auth::id(), // User login
            'event_id' => $event->id, // Event
            'status' => 'pending', // Status
        ]);

        return redirect()->route('dashboard') // Redirect dashboard
            ->with('success', 'Event berhasil disubmit dan terdaftar!'); // Pesan sukses
    }

    public function update(Request $request, int $id): RedirectResponse // Update event
    {
        $event = Event::findOrFail($id); // Ambil event

        if ($event->status !== 'pending') { // Jika bukan pending
            return redirect()->route('user.event.history') // Redirect
                ->with('error', 'Event yang sudah disetujui tidak dapat diubah.'); // Error
        }

        $event->update($request->all()); // Update data

        return redirect()->route('user.event.history') // Redirect
            ->with('success', 'Event berhasil diperbarui!'); // Pesan sukses
    }

    public function index(): View // Dashboard
    {
        $events = Event::where('status', 'approved')->latest()->get(); // Ambil event
        return view('dashboard.dashboard', compact('events')); // View dashboard
    }

    public function show(int $id): View // Detail event
    {
        $event = Event::findOrFail($id); // Ambil event
        return view('event-detail', compact('event')); // View detail
    }

    public function browse(): View // Browse event
    {
        $events = Event::where('status', 'approved')->latest()->get(); // Event approved
        return view('browse-events', compact('events')); // View browse
    }

    public function showHistory(): View|RedirectResponse // Riwayat event
    {
        if (! Auth::check()) { // Jika belum login
            return redirect()->route('login'); // Redirect login
        }

        $registrations = EventRegistration::where('user_id', Auth::id()) // Ambil history
            ->with('event') // Load event
            ->latest() // Urut terbaru
            ->paginate(10); // Pagination

        return view('event-history', compact('registrations')); // View history
    }

    public function showRegistration(int $id): View // Detail registrasi
    {
        $registration = EventRegistration::with('event') // Load event
            ->where('user_id', Auth::id()) // User login
            ->findOrFail($id); // Cari data

        return view('registration-detail', compact('registration')); // View detail
    }
}
