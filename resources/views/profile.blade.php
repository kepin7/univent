@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/userprofile.css') }}">
@endpush

@section('title', 'User Profile - Univent')

@section('content')
    <div class="profile-page-container">
        <div class="profile-card">
            <div class="profile-sidebar">

                {{-- AWAL PERBAIKAN LOGIKA AVATAR --}}
                @if (Auth::user()->avatar)
                    {{-- Avatar base64 dari database --}}
                    <img src="data:image/jpeg;base64,{{ Auth::user()->avatar }}" alt="Profile Picture" class="profile-avatar">
                @else
                    {{-- Default avatar --}}
                    <img src="{{ asset(path: 'images/default-avatar.svg') }}" alt="Default Profile Picture" class="profile-avatar">
                @endif
                <h3 class="profile-name">{{ $user->name }}</h3>
            </div>

            <div class="profile-main">
                <h2>Halo, {{ $user->name }}!</h2>

                <div class="profile-details">
                    <div class="profile-detail-item">
                        <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                        </svg>
                        <div class="detail-text">
                            <span>Email</span>
                            <p>{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="profile-detail-item">
                        <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                        </svg>
                        <div class="detail-text">
                            <span>Birthday</span>
                            <p>{{ $user->profile?->birthday ? \Carbon\Carbon::parse($user->profile->birthday)->format('d F Y') : 'Belum diatur' }}
                            </p>
                        </div>
                    </div>
                    <div class="profile-detail-item">
                        <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.23 1.02l-2.2 2.2z" />
                        </svg>
                        <div class="detail-text">
                            <span>Phone</span>
                            {{-- PERBAIKI DI SINI: Pastikan tidak ada '@' --}}
                            <p>{{ $user->profile?->phone ?? 'Belum diatur' }}</p>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="{{ route('user.event.history') }}" class="btn-event-history">
                        Riwayat Form Event
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn-edit-profile">Edit Profile</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-logout">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
