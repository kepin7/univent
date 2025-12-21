    @extends('layouts.app')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/browseevents.css') }}">
        <link rel="stylesheet" href="{{ asset('css/eventcard.css') }}">


        <script src="{{ asset('js/browseevents-filter.js') }}"></script>
        <script src="{{ asset('js/dropdown.js') }}"></script>
    @endpush

    @section('title', 'Browse Events - Univent')

    @section('content')
        <div class="browse-container">
            <div class="browse-header">
                <h1>Browse Events</h1>
                <p>Discover exciting events happening at Telkom University Purwokerto</p>
            </div>

            <div class="filter-bar">
                <div class="search-input">
                    <input type="text" placeholder="Search Events">
                </div>

                {{-- CATEGORY DROPDOWN --}}
                <div class="custom-dropdown">
                    <div class="dropdown-selected">
                        <span>All Categories</span>
                        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z" />
                        </svg>
                    </div>
                    <ul class="dropdown-options">
                        <li class="active" data-value="all">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>All Categories</span>
                        </li>
                        <li data-value="Seminar">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Seminar</span>
                        </li>
                        <li data-value="Workshop">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Workshop</span>
                        </li>
                        <li data-value="Competition">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Competition</span>
                        </li>
                        <li data-value="Gathering">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Gathering</span>
                        </li>
                        <li data-value="Other">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Other</span>
                        </li>
                    </ul>
                </div>

                {{-- ORGANIZER DROPDOWN (FIXED) --}}
                <div class="custom-dropdown">
                    <div class="dropdown-selected">
                        <span>All Organizers</span>
                        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z" />
                        </svg>
                    </div>
                    <ul class="dropdown-options">
                        <li class="active" data-value="all">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>All Organizers</span>
                        </li>

                        {{-- ✅ LABEL Student Association / VALUE student_association --}}
                        <li data-value="student_association">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Student Association</span>
                        </li>

                        <li data-value="lecturer">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>Lecturer</span>
                        </li>

                        <li data-value="external">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                            </svg>
                            <span>External</span>
                        </li>
                    </ul>
                </div>

                <button class="clear-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" />
                    </svg>
                    <span>Clear</span>
                </button>
            </div>

            <div class="cards-grid" id="events-container">
                @foreach ($events as $event)
                    <div class="card" data-category="{{ $event->event_category }}"
                        data-organizer="{{ $event->organizer_type }}">
                        <div class="card-image">
                            <img src="data:image/jpeg;base64,{{ $event->event_poster }}" alt="Event Poster">

                        </div>

                        <div class="card-content">
                            <div class="tags">
                                @if ($event['tags'])
                                    @foreach (explode(',', $event['tags']) as $tag)
                                        <span class="tag">{{ $tag }}</span>
                                    @endforeach
                                @endif
                            </div>

                            <h3 class="card-title">{{ $event['event_title'] }}</h3>
                            <p class="card-desc">{{ $event['event_description'] }}</p>

                            <div class="card-meta">

                                <div class="card-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path
                                            d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z" />
                                    </svg>
                                    <span>{{ date('D, M d, Y, H:i A', strtotime($event['start_date'] . ' ' . $event['start_time'])) }}</span>
                                </div>

                                <div class="card-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <span>{{ $event['event_location'] }}</span>
                                </div>

                                <div class="card-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path
                                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                    </svg>

                                    {{-- ✅ DISPLAY ORGANIZER RAPI TANPA UNDERSCORE --}}
                                    <span>by {{ ucwords(str_replace('_', ' ', $event['organizer_type'])) }}</span>
                                </div>

                            </div>

                            <a href="{{ route('events.show', $event->id) }}" class="btn-details">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    @endsection
