@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/eventdetail.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/popup.js') }}"></script>
@endpush
@section('title', $event->event_title . ' - Univent')

@section('content')
    <div class="detail-page-container">
        <a href="{{ route('admin.event-list') }}" class="back-link back-btn-pill"> <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
            </svg>
            <span>Back to event list</span>
        </a>

        <div class="event-detail-grid">
            <!-- Main content -->
            <div class="event-main-content">
                <!-- Gambar -->
                <img class="clickable-poster event-poster" src="data:image/jpeg;base64,{{ $event->event_poster }}"
                    alt="Event Poster" style="cursor:pointer; width:200px;">

                <!-- Popup / Modal -->
                <div id="popupModal" class="modal">
                    <span class="close">&times;</span>
                    <img class="modal-content" id="popupImage">
                    <div id="caption"></div>
                </div>

                <div class="tags">
                    @if ($event->tags)
                        @foreach (explode(',', $event->tags) as $tag)
                            <span class="tag">{{ $tag }}</span>
                        @endforeach
                    @endif
                </div>

                <h1>{{ $event->event_title }}</h1>

                <p class="organizer-name">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span>{{ $event->organizer_name }}</span>
                </p>

                <hr class="divider-line">

                <h2>About Event</h2>
                <p class="event-long-description">
                    {{ $event->event_description }}
                </p>
            </div>

            <!-- Sidebar -->
            <div class="event-sidebar">
                <div class="info-card">
                    <div class="info-item">
                        <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                        </svg>
                        <div class="info-text">
                            <span>Start</span>
                            <p>{{ date('D, d M Y, H:i', strtotime($event->start_date . ' ' . $event->start_time)) }}</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                        </svg>
                        <div class="info-text">
                            <span>End</span>
                            <p>{{ date('D, d M Y, H:i', strtotime($event->end_date . ' ' . $event->end_time)) }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-item">
                        <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                        <div class="info-text">
                            <span>Location</span>
                            <p>{{ $event->event_location }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-item">
                        <svg class="info-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                        <div class="info-text">
                            <span>Contact Information</span>
                            <p>{{ $event->contact_person }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ $event->registration_link ?? '#' }}" class="btn-register">Register Event</a>
            </div>
        </div>
    </div>
@endsection
