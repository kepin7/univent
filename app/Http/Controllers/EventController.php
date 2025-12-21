<?php

namespace App\Http\Controllers; // Namespace controller

use App\Models\Event; // Model Event
use App\Models\EventRegistration; // Model EventRegistration
use Illuminate\Http\Request; // Class Request
use Illuminate\Support\Facades\Auth; // Facade Auth
use Illuminate\View\View; // Tipe return View
use Illuminate\Http\RedirectResponse; // Tipe return RedirectResponse

class EventController extends Controller // Controller Event
{
    public function __construct() // Constructor
    {
        // Middleware auth hanya untuk method tertentu
        $this->middleware('auth')->only([
            'create', // Butuh login
            'store', // Butuh login
            'update', // Butuh login
            'showHistory', // Butuh login
            'showRegistration', // Butuh login
        ]);
    }

    // Redirect jika belum login
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() // Jika request JSON
            ? null // Tidak redirect
            : route('login'); // Redirect ke login
    }

    // =========================
    // FORM SUBMIT / EDIT EVENT
    // =========================
    public function create(Request $request): View|RedirectResponse
    {
        $event = null; // Default event kosong

        if ($request->has('edit')) { // Jika mode edit
            $eventId = $request->query('edit'); // Ambil ID event dari query
            
            /** @var \App\Models\Event $event */
            $event = Event::findOrFail($eventId); // Cari event

            // Cek apakah user pemilik event
            $isOwner = EventRegistration::where('event_id', $event->id) // Filter event
                ->where('user_id', Auth::id()) // Filter user
                ->exists(); // Cek keberadaan

            if (! $isOwner) { // Jika bukan pemilik
                return redirect()->route('user.event.history') // Redirect
                    ->with('error', 'Anda tidak berhak mengubah event ini.'); // Pesan error
            }

            if ($event->status !== 'pending') { // Jika event bukan pending
                return redirect()->route('user.event.history') // Redirect
                    ->with('error', 'Event yang sudah disetujui tidak dapat diubah.'); // Error
            }
        }

        return view('submit-event', compact('event')); // Tampilkan form
    }

    // =========================
    // SIMPAN EVENT BARU
    // =========================
    public function store(Request $request): RedirectResponse
    {
        $request->validate([ // Validasi input
            'event_title' => 'required|string|max:255', // Judul
            'organizer_name' => 'required|string|max:255', // Nama penyelenggara
            'organizer_type' => 'required|string', // Tipe penyelenggara
            'event_category' => 'required|string', // Kategori
            'event_description' => 'required|string', // Deskripsi
            'start_date' => 'required|date', // Tanggal mulai
            'start_time' => 'required', // Jam mulai
            'end_date' => 'required|date|after_or_equal:start_date', // Tanggal selesai
            'end_time' => 'required', // Jam selesai
            'event_location' => 'required|string', // Lokasi
            'registration_link' => 'nullable|url', // Link pendaftaran
            'contact_person' => 'required|string', // Kontak
            'event_poster' => 'nullable|image|mimes:jpg,jpeg,png|max:4096', // Poster
        ]);

        $posterData = null; // Default poster kosong

        if ($request->hasFile('event_poster')) { // Jika ada file
            $file = $request->file('event_poster'); // Ambil file
            if ($file && $file->getRealPath()) { // Pastikan file valid
                $content = file_get_contents($file->getRealPath()); // Baca file
                if ($content !== false) { // Jika berhasil dibaca
                    $posterData = base64_encode($content); // Encode BASE64
                }
            }
        }

        $event = Event::create([ // Simpan event
            'user_id' => Auth::id(), // ID user
            'event_title' => $request->event_title, // Judul
            'organizer_name' => $request->organizer_name, // Organizer
            'organizer_type' => $request->organizer_type, // Tipe
            'event_category' => $request->event_category, // Kategori
            'event_description' => $request->event_description, // Deskripsi
            'start_date' => $request->start_date, // Tanggal mulai
            'start_time' => $request->start_time, // Jam mulai
            'end_date' => $request->end_date, // Tanggal selesai
            'end_time' => $request->end_time, // Jam selesai
            'event_location' => $request->event_location, // Lokasi
            'registration_link' => $request->registration_link, // Link
            'contact_person' => $request->contact_person, // Kontak
            'event_poster' => $posterData, // Poster BASE64
            'status' => 'pending', // Status awal
        ]);

        if (Auth::check()) { // Jika user login
            EventRegistration::create([ // Buat registrasi
                'user_id' => Auth::id(), // User ID
                'event_id' => $event->id, // Event ID
                'status' => 'pending', // Status
            ]);
        }

        return redirect()->route('dashboard') // Redirect
            ->with('success', 'Event berhasil disubmit dan terdaftar!'); // Pesan sukses
    }

    // =========================
    // UPDATE EVENT
    // =========================
    public function update(Request $request, int $id): RedirectResponse
    {
        $event = Event::findOrFail($id); // Cari event

        if ($event->status !== 'pending') { // Jika bukan pending
            return redirect()->route('user.event.history') // Redirect
                ->with('error', 'Event yang sudah disetujui tidak dapat diubah.'); // Error
        }

        $isOwner = EventRegistration::where('event_id', $event->id) // Filter event
            ->where('user_id', Auth::id()) // Filter user
            ->exists(); // Cek kepemilikan

        if (! $isOwner) { // Jika bukan pemilik
            return redirect()->route('user.event.history') // Redirect
                ->with('error', 'Anda tidak berhak mengubah event ini.'); // Error
        }

        $posterData = $event->event_poster; // Poster lama

        if ($request->hasFile('event_poster')) { // Jika upload baru
            $file = $request->file('event_poster'); // Ambil file
            if ($file && $file->getRealPath()) { // Validasi
                $content = file_get_contents($file->getRealPath()); // Baca file
                if ($content !== false) { // Jika berhasil
                    $posterData = base64_encode($content); // Encode
                }
            }
        }

        $event->update([ // Update event
            'event_title' => $request->event_title,
            'organizer_name' => $request->organizer_name,
            'organizer_type' => $request->organizer_type,
            'event_category' => $request->event_category,
            'event_description' => $request->event_description,
            'start_date' => $request->start_date,
            'start_time' => $request->start_time,
            'end_date' => $request->end_date,
            'end_time' => $request->end_time,
            'event_location' => $request->event_location,
            'registration_link' => $request->registration_link,
            'contact_person' => $request->contact_person,
            'event_poster' => $posterData, // Poster BASE64
        ]);

        return redirect()->route('user.event.history') // Redirect
            ->with('success', 'Event berhasil diperbarui!'); // Pesan sukses
    }
}