<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Univent Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/reset-base.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/navavatar.css') }}" />

    <script src="{{ asset('js/ddl.js') }}"></script>
    <script src="{{ asset('js/scriptdashboard.js') }}"></script>
    <script src="{{ asset('js/hamburg.js') }}"></script>


    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">

    <link rel="stylesheet" href="{{ asset('css/herosection.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/eventcard.css') }}" />
</head>

<body>
    <nav>
        <div class="nav-container">
            <div class="nav-left">
                <img src="{{ asset('images/univent-logo.png') }}" alt="Univent Logo" class="nav-logo"
                    style="height: 40px;">

            </div>
            <div class="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="nav-menu">
                <a href="/" class="nav-link {{ Request::is('/') ? 'active' : '' }}" tabindex="0">Home</a>
                <a href="/submit-event" class="nav-link {{ Request::is('submit-event') ? 'active' : '' }}">Submit
                    Event</a>
                @auth
                    @if (!Auth::user()->isAdmin())
                        <a href="/contact" class="nav-link {{ Request::is('contact') ? 'active' : '' }}"
                            tabindex="0">Contact</a>
                    @endif
                @else
                    <a href="/contact" class="nav-link {{ Request::is('contact') ? 'active' : '' }}"
                        tabindex="0">Contact</a>
                @endauth

                @auth

                    @if (Auth::user()->isAdmin())
                        <a href="/admin/event-list" class="nav-link">Event List</a>
                    @endif

                    <!-- Link ke profile -->
                    <a href="{{ route('profile.show') }}"
                        class="nav-link user-profile {{ Request::is('profile') || Request::is('profile/edit') ? 'active' : '' }}">
                        <span>{{ Auth::user()->name }}</span>

                        @if (Auth::user()->avatar)
                            <img src="data:image/jpeg;base64,{{ Auth::user()->avatar }}" alt="Profile Picture"
                                class="navbar-avatar">
                        @else
                            <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Profile Picture"
                                class="navbar-avatar">
                        @endif
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-link {{ Request::is('login') ? 'active' : '' }}">Log in</a>
                @endauth

            </div>
        </div>
    </nav>
    <section class="hero" role="banner" aria-label="Hero section">
        <h1>Discover Campus Events at
            <br>
            <span class="highlight-red">Telkom University Purwokerto</span>
        </h1>
        <p>Stay connected with seminars, workshops, competitions, and gatherings organized by student associations,
            lecturers, and external partners.</p>
        <div class="btn-group">
            <a href="/browse-events" class="btn-red" aria-label="Browse Events">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                </svg>
                <span>Browse Events</span>
            </a>
            <a href="{{ route('submit-event') }}" class="btn-outline-red" aria-label="Submit Event">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                </svg>
                <span>Submit Event</span>
            </a>
        </div>
    </section>

    <section class="upcoming-events" aria-labelledby="upcoming-events-title">
        <h2 id="upcoming-events-title">Upcoming Events</h2>
        <p class="description">Explore the latest events happening on campus. Join and enrich your university
            experience!</p>

        <div class="cards-grid" id="events-container" aria-live="polite" aria-relevant="additions">

            @foreach ($events as $event)
                <div class="card">
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
                                <span>
                                    {{ date('D, M d, Y, H:i A', strtotime($event['start_date'] . ' ' . $event['start_time'])) }}
                                </span>
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
                                <span>by {{ $event['organizer_type'] }}</span>
                            </div>
                        </div>
                        <a href="{{ route('events.show', $event->id) }}" class="btn-details">View Details</a>
                    </div>
                </div>
            @endforeach

        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-col footer-brand" aria-label="Brand information">
                <img src="{{ asset('images/univent-logo2.png') }}" alt="Univent Logo" class="footer-brand-logo"
                    style="height: 65px; width: 180px; margin-bottom: 0.5rem;">
                <p class="footer-brand-desc">
                    Univent is your gateway to all campus events at Telkom University Purwokerto, connecting students,
                    lecturers, and partners.
                </p>
            </div>
            <div class="footer-col footer-links" aria-label="Quick links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="/browse-events">Browse Events</a></li>
                    <li><a href="/submit-event">Submit Event</a></li>
                    @auth
                        @if (!Auth::user()->isAdmin())
                            <li><a href="/contact">Contact Us</a></li>
                        @endif
                    @else
                        <li><a href="/contact">Contact Us</a></li>
                    @endauth

                </ul>
            </div>
            <div class="footer-col footer-categories" aria-label="Event categories">
                <h3>Categories</h3>
                <ul>
                    <li data-value="seminar">
                        <a href="/browse-events?category=Seminar" tabindex="0">Seminars</a>
                    </li>

                    <li data-value="Workshop">
                        <a href="/browse-events?category=Workshop" tabindex="0">Workshops</a>
                    </li>

                    <li data-value="competition">
                        <a href="/browse-events?category=Competition" tabindex="0">Competitions</a>
                    </li>

                    <li data-value="gathering">
                        <a href="/browse-events?category=Gathering" tabindex="0">Gatherings</a>
                    </li>

                    <li data-value="other">
                        <a href="/browse-events?category=Other" tabindex="0">Others</a>
                    </li>
                </ul>



            </div>
            <div class="footer-col footer-contact" aria-label="Contact information">
                <h3>Contact Info</h3>
                <p>Telkom University Purwokerto</p>
                <p>Jl. DI Panjaitan No.128, Purwokerto</p>
                <p>Email: <a
                        href="https://mail.google.com/mail/?view=cm&fs=1&to=univenttelkom@gmail.com
&su=Pertanyaan%20Event&body=Halo%2C%20saya%20ingin%20bertanya%20tentang%20event..."
                        target="_blank" style="color:#fff; text-decoration: underline;">univenttelkom@gmail.com</a>
                </p>

            </div>
        </div>
        </div>
        <div class="footer-bottom" role="contentinfo">
            &copy; 2025 Univent - Oxy Project. All rights reserved.
        </div>
    </footer>

    <script src="{{ asset('js/scriptdashboard.js') }}"></script>
</body>
@include('partials.sweetalert')

</html>
