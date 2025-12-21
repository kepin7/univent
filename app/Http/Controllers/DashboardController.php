<?php

namespace App\Http\Controllers; // Namespace controller

use Illuminate\View\View; // Tipe return View

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama
     */
    public function index(): View
    {
        // Ambil event yang sudah disetujui, urut terbaru
        $events = \App\Models\Event::where('status', 'approved') // Filter status approved
            ->latest() // Urutkan berdasarkan terbaru
            ->get(); // Ambil semua data

        // Kirim data event ke view dashboard
        return view('dashboard.dashboard', compact('events'));
    }
}