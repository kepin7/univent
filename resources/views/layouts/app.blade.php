<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Univent')</title>

    <link rel="stylesheet" href="{{ asset('css/reset-base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <script src="{{ asset('js/hamburg.js') }}"></script>

    @stack('styles')

    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">
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

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-col footer-brand">
                <img src="{{ asset('images/univent-logo2.png') }}" alt="Univent Logo"
                    style="height: 65px; width: 180px; margin-bottom: 0.5rem;">
                <p class="footer-brand-desc">
                    Univent is your gateway to all campus events at Telkom University Purwokerto, connecting students,
                    lecturers, and partners.
                </p>
            </div>

            <div class="footer-col footer-links">
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


            <div class="footer-col footer-categories">
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

            <div class="footer-col footer-contact">
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

        <div class="footer-bottom">
            &copy; 2025 Univent - Oxy Project. All rights reserved.
        </div>
    </footer>

    @include('partials.sweetalert')
    @stack('scripts')
    <script>
        function toggleMenu() {
            const navMenu = document.querySelector('.nav-menu');
            const hamburger = document.querySelector('.hamburger');

            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        }
    </script>
</body>

</html>
