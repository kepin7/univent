@extends('layouts.app')

@section('title', 'Riwayat Event - Univent')

@section('content')
    <div class="history-page-container">
        <div class="history-card">
            <h1>Riwayat Pengisian Form Event</h1>
            <p class="subtitle">Daftar semua event yang pernah Anda daftarkan.</p>

            @if ($registrations->isEmpty())
                {{-- Tampilan jika tidak ada riwayat --}}
                <div class="empty-state">
                    <p>Anda belum mendaftar ke event apa pun.</p>
                    <a href="{{ route('browse-events') }}" class="btn-primary">Telusuri Event Sekarang</a>
                </div>
            @else
                {{-- Tampilan jika ada riwayat --}}
                <div class="registration-list">
                    @foreach ($registrations as $registration)
                        <div class="registration-item">
                            <div class="event-info">
                                {{-- Asumsi: Setiap registrasi memiliki relasi 'event' --}}
                                <h3>{{ $registration->event->event_title ?? 'Nama Event Tidak Diketahui' }}</h3>
                                <p class="date">
                                    Tanggal Pendaftaran:
                                    <span>{{ $registration->created_at->format('d F Y') }}</span>
                                </p>
                                <p class="status">
                                    Status:
                                    <span class="status-{{ $registration->status }}">
                                        {{ ucfirst($registration->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="item-actions">
                                {{-- Tombol untuk melihat detail form yang diisi --}}
                                <a href="{{ route('registration.show', $registration->id) }}" class="btn-detail">Lihat
                                    Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Anda bisa menambahkan paginasi di sini jika diperlukan --}}
                {{-- <div class="pagination-links">
                    {{ $registrations->links() }}
                </div> --}}
            @endif
        </div>
    </div>
@endsection

@push('styles')
    {{-- Jangan lupa tambahkan CSS khusus untuk halaman ini --}}
    <link rel="stylesheet" href="{{ asset('css/eventhistory.css') }}">
@endpush
