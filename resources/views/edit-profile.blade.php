@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/userprofile-edits.css') }}">
@endpush

@section('title', 'Edit Profile - Univent')

@section('content')
    <div class="profile-page-container">
        <div class="profile-card">

            {{-- ===================== --}}
            {{--        SIDEBAR       --}}
            {{-- ===================== --}}
            <div class="profile-sidebar">

                {{-- AVATAR --}}
                @if ($user->avatar)
                    <img id="avatar-preview" class="profile-avatar" src="data:image/jpeg;base64,{{ $user->avatar }}"
                        data-default="{{ asset('images/default-avatar.svg') }}">
                @else
                    <img id="avatar-preview" class="profile-avatar" src="{{ asset('images/default-avatar.svg') }}"
                        data-default="{{ asset('images/default-avatar.svg') }}">
                @endif

                <h3 class="profile-name">{{ $user->name }}</h3>

                <div class="avatar-actions">

                    <input type="file" name="avatar_raw" id="avatar-upload-input" accept="image/*" style="display:none;">
                    <label for="avatar-upload-input" class="label-change-photo">Change</label>

                    {{-- REMOVE hanya tampil jika ada avatar --}}
                    @if ($user->avatar)
                        <button type="button" id="btn-remove-photo" class="btn-remove-photo">Remove</button>
                    @endif

                </div>
            </div> {{-- END SIDEBAR --}}


            {{-- ===================== --}}
            {{--        MAIN FORM      --}}
            {{-- ===================== --}}
            <div class="profile-main">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                        <h2 style="margin: 0;">Halo, </h2>
                        <div style="position: relative; display: flex; align-items: center;">
                            {{-- Input Nama --}}
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                style="font-size: 24px; font-weight: bold; border: none; border-bottom: 2px solid #ddd; outline: none; background: transparent; padding-right: 30px;"
                                required>
                        </div>
                    </div>

                    {{-- hidden fields --}}
                    <input type="hidden" name="new_avatar_temp" id="new-avatar-temp">
                    <input type="hidden" name="remove_avatar" id="remove-avatar" value="0">
                    <div class="profile-details edit-mode">

                        <div class="profile-detail-item">
                            <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                            <div class="detail-text">
                                <span>Email</span>
                                <p style="color: #6c757d; padding-top: 8px; padding-bottom: 8px; font-size: 16px;">
                                    {{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="profile-detail-item">
                            <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z" />
                            </svg>
                            <div class="detail-text">
                                <span>Birthday</span>
                                <input type="date" name="birthday"
                                    value="{{ old('birthday', $user->profile?->birthday ? \Carbon\Carbon::parse($user->profile->birthday)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="profile-detail-item">
                            <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.23 1.02l-2.2 2.2z" />
                            </svg>
                            <div class="detail-text">
                                <span>Phone</span>
                                <input type="tel" name="phone" value="{{ old('phone', $user->profile?->phone) }}"
                                    placeholder="Enter your phone number">
                            </div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="btn-save-profile">Save Profile</button>
                        <a href="{{ route('profile.show') }}" class="btn-cancel-edit">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </form>
    </div> {{-- END MAIN --}}

    </div>
    </div>


    @push('scripts')
        <script src="{{ asset('js/profile-edit.js') }}"></script>
    @endpush

@endsection
