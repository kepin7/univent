@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/submitevent.css') }}">

    <link rel="stylesheet" href="{{ asset('css/browseevents.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/file-upload-preview.js') }}"></script>

    <script src="{{ asset('js/dropdown.js') }}"></script>
@endpush

@section('title', (isset($event) ? 'Edit Event' : 'Submit Event') . ' - Univent')

@section('content')
    @auth
        {{-- 🆕 Tentukan mode dan rute berdasarkan variabel $event yang dikirim dari Controller --}}
        @php
            $isEditMode = isset($event);
            $formAction = $isEditMode ? route('submit-event.update', $event->id) : route('submit-event');
        @endphp

        <div class="submit-page-container">
            <div class="form-container">
                <h1>{{ $isEditMode ? 'Edit Event' : 'Submit Event' }}</h1>
                <p class="subtitle">Share your event with the Telkom University Purwokerto community. All fields
                    marked with *
                    are required.</p>

                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- 🆕 Jika Edit, gunakan method PUT --}}
                    @if ($isEditMode)
                        @method('PUT')
                    @endif

                    <div class="form-group">
                        <label for="event_title">Event Title</label>
                        <input type="text" id="event_title" name="event_title" placeholder="Enter Event Title" required
                            value="{{ old('event_title', $event->event_title ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="organizer_name">Organizer name</label>
                        <input type="text" id="organizer_name" name="organizer_name" placeholder="Enter Organizer Title"
                            required value="{{ old('organizer_name', $event->organizer_name ?? '') }}">

                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="organizer_type">Organizer type</label>
                            <div class="custom-dropdown"
                                data-initial-value="{{ old('organizer_type', $event->organizer_type ?? '') }}">
                                <div class="dropdown-selected">

                                    <span>{{ old('organizer_type', $event->organizer_type ?? 'Select type') }}</span>
                                    <svg class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">

                                        <path d="M7 10l5 5 5-5z" />

                                    </svg>
                                </div>
                                <ul class="dropdown-options">
                                    <li data-value="student_association"><svg class="checkmark"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Student Association</span></li>
                                    <li data-value="lecturer"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Lecturer</span></li>
                                    <li data-value="external"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>External</span></li>
                                </ul>
                            </div>
                            <input type="hidden" name="organizer_type" id="organizer_type"
                                value="{{ old('organizer_type', $event->organizer_type ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="event_category">Event Category</label>
                            <div class="custom-dropdown"
                                data-initial-value="{{ old('event_category', $event->event_category ?? '') }}">
                                <div class="dropdown-selected">

                                    <span>{{ old('event_category', $event->event_category ?? 'Select Category') }}</span>
                                    <svg class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">

                                        <path d="M7 10l5 5 5-5z" />

                                    </svg>
                                </div>
                                <ul class="dropdown-options">
                                    <li data-value="seminar"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Seminar</span></li>
                                    <li data-value="workshop"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Workshop</span></li>
                                    <li data-value="competition"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Competition</span></li>
                                    <li data-value="gathering"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Gathering</span></li>
                                    <li data-value="other"><svg class="checkmark" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24">

                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />

                                        </svg><span>Other</span></li>
                                </ul>
                            </div>
                            <input type="hidden" name="event_category" id="event_category"
                                value="{{ old('event_category', $event->event_category ?? '') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="event_description">Event description</label>

                        <textarea id="event_description" name="event_description" rows="6" placeholder="Describe your event..." required>{{ old('event_description', $event->event_description ?? '') }}</textarea>

                    </div>

                    <div class="form-group">
                        <label>Event Schedule</label>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date" class="sub-label">Start date</label>
                                <input type="date" id="start_date" name="start_date" required
                                    value="{{ old('start_date', $event->start_date ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="start_time" class="sub-label">Start time</label>
                                <input type="time" id="start_time" name="start_time" required
                                    value="{{ old('start_time', $event->start_time ?? '') }}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="end_date" class="sub-label">End date</label>
                                <input type="date" id="end_date" name="end_date" required
                                    value="{{ old('end_date', $event->end_date ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="end_time" class="sub-label">End time</label>
                                <input type="time" id="end_time" name="end_time" required
                                    value="{{ old('end_time', $event->end_time ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="event_location">Event location</label>
                        <input type="text" id="event_location" name="event_location" placeholder="Enter event location"
                            required value="{{ old('event_location', $event->event_location ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="registration_link">Registration link</label>
                        <input type="url" id="registration_link" name="registration_link" placeholder="Enter link"
                            required value="{{ old('registration_link', $event->registration_link ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="contact_person">Contact person</label>
                        <input type="text" id="contact_person" name="contact_person" placeholder="Name (WhatsApp)"
                            required value="{{ old('contact_person', $event->contact_person ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label for="event_poster">Event Poster</label>
                        <div class="file-upload-area" id="file-upload-area">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">

                                <path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 4h14v-2H5v2z" />

                            </svg>
                            <p class="upload-text">Drag & drop your file here</p>
                            <span class="upload-subtext">or click to browse</span>
                            <span class="upload-subtext" style="font-size: 12px; color: #777;">
                                Max file size: 3 MB
                            </span>

                            {{-- 🆕 Jika mode Edit dan ada poster lama, hapus required --}}
                            @php
                                $isExistingPoster = $isEditMode && $event->event_poster;
                            @endphp
                            <input type="file" id="event_poster" name="event_poster" class="file-input" accept="image/*"
                                {{ $isExistingPoster ? '' : 'required' }}>

                            <div id="preview-container"
                                style="display:{{ $isExistingPoster ? 'block' : 'none' }}; margin-top:10px; position: relative;">

                                {{-- 🆕 Tampilkan poster lama jika ada --}}
                                @if ($isExistingPoster)
                                    <img id="preview-image" src="data:image/jpeg;base64,{{ $event->event_poster }}"
                                        alt="Preview" style="max-width:100%; border-radius:8px;">
                                @else
                                    <img id="preview-image" src="#" alt="Preview"
                                        style="max-width:100%; border-radius:8px;">
                                @endif

                                <button type="button" id="remove-preview"
                                    style="
                                position: absolute; top:5px; right:5px; background:red; color:white;
                                border:none; border-radius:4px; padding:2px 6px; cursor:pointer;">Remove</button>

                            </div>

                        </div>
                    </div>



                    <div class="form-buttons">
                        <button type="submit"
                            class="btn-submit">{{ $isEditMode ? 'Update Event' : 'Submit Event' }}</button>
                        <button type="reset" class="btn-clear">Clear Form</button>
                    </div>

                </form>
            </div>
        </div>
    @endauth

@endsection
