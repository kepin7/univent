@extends('layouts.app')

@section('title', 'Detail Pendaftaran - ' . ($registration->event->event_title ?? 'Detail'))

@section('content')
    <div class="detail-page-container">
        <div class="detail-card">

            {{-- 1. Tombol Aksi --}}
            <div class="detail-actions">
                <a href="{{ route('user.event.history') }}" class="back-btn-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                    </svg>
                    Back to history
                </a>
                @if (Auth::id() == $registration->user_id && strtolower($registration->event->status) === 'pending')
                    <a href="{{ route('submit-event.form', ['edit' => $registration->event->id]) }}" class="btn-edit">
                        Edit
                    </a>
                @endif
            </div>

            {{-- 2. Judul Halaman --}}
            <h1>Registration Details</h1>
            <p class="subtitle">Your Event Informations</p>

            {{-- 3. Status --}}
            <div class="detail-field status-field">
                <label>Status</label>
                <div class="detail-field-value">
                    <span class="status-badge status-{{ strtolower($registration->status) }}">
                        {{ ucfirst(strtolower($registration->status)) }}
                    </span>
                </div>
            </div>

            {{-- 4. GRID KONTEN UTAMA --}}
            <div class="detail-content-grid">

                {{-- Event Title --}}
                <div class="detail-field event-title-field">
                    <label>Event Title</label>
                    <div class="event-title-value"> {{ $registration->event->event_title ?? 'N/A' }}
                    </div>
                </div>

                {{-- Organizer Name --}}
                <div class="detail-field organizer-field">
                    <label>Organizer Name</label>
                    <div class="detail-field-value organizer-value">
                        <span class="organizer-badge">{{ $registration->event->organizer_name ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- NEW – Organizer Type --}}
                <div class="detail-field">
                    <label>Organizer type</label>
                    <div class="detail-field-value">
                        {{ $registration->event->organizer_type ?? 'N/A' }}
                    </div>
                </div>

                {{-- NEW – Event Category --}}
                <div class="detail-field">
                    <label>Event Category</label>
                    <div class="detail-field-value">
                        {{ $registration->event->event_category ?? 'N/A' }}
                    </div>
                </div>

                {{-- Description --}}
                <div class="detail-field grid-span-2">
                    <label>Event description</label>
                    <div class="detail-field-value" style="min-height: 70px;">
                        {{ $registration->event->event_description ?? 'Tidak ada deskripsi.' }}
                    </div>
                </div>

                {{-- Schedule Title --}}
                <div class="detail-field grid-span-2" style="margin-bottom: 0;">
                    <label style="font-size: 1.1rem; color: #495057; margin-bottom: 0;">Event Schedule</label>
                </div>

                {{-- Start Date --}}
                <div class="detail-field">
                    <label class="sub-label">Start date</label>
                    <div class="detail-field-value">
                        {{ \Carbon\Carbon::parse($registration->event->start_date)->format('d/m/Y') }}
                    </div>
                </div>

                {{-- Start Time --}}
                <div class="detail-field">
                    <label class="sub-label">Start time</label>
                    <div class="detail-field-value">
                        {{ \Carbon\Carbon::parse($registration->event->start_time)->format('H:i') }}
                    </div>
                </div>

                {{-- End Date --}}
                <div class="detail-field">
                    <label class="sub-label">End date</label>
                    <div class="detail-field-value">
                        {{ \Carbon\Carbon::parse($registration->event->end_date)->format('d/m/Y') }}
                    </div>
                </div>

                {{-- End Time --}}
                <div class="detail-field">
                    <label class="sub-label">End time</label>
                    <div class="detail-field-value">
                        {{ \Carbon\Carbon::parse($registration->event->end_time)->format('H:i') }}
                    </div>
                </div>

                {{-- Location --}}
                <div class="detail-field grid-span-2">
                    <label>Event location</label>
                    <div class="detail-field-value">{{ $registration->event->event_location ?? 'N/A' }}</div>
                </div>

                {{-- Registration link --}}
                <div class="detail-field grid-span-2 link-field">
                    <label>Registration link</label>
                    <div class="detail-field-value">
                        <a href="{{ $registration->event->registration_link }}" target="_blank"
                            style="color:#ff0000; text-decoration: underline;">
                            {{ $registration->event->registration_link ?? 'N/A' }}
                        </a>
                    </div>
                </div>

                {{-- Contact Person --}}
                <div class="detail-field grid-span-2">
                    <label>Contact person</label>
                    <div class="detail-field-value">{{ $registration->event->contact_person ?? 'N/A' }}</div>
                </div>

                {{-- Poster --}}
                @if ($registration->event->event_poster)
    <div class="detail-field grid-span-2">
        <label>Event Poster</label>
        <div class="detail-field-value poster-preview">
            <img 
                src="data:image/jpeg;base64,{{ $registration->event->event_poster }}" 
                alt="Poster {{ $registration->event->event_title }}" 
                class="event-poster-image"
            >
        </div>
    </div>
@endif


            </div>

        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/registration-detail.css') }}">
@endpush
