@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('title', 'Contact Us - Univent')

@section('content')
    <div class="contact-page-container">
        <div class="contact-card">
            <div class="contact-form-section">
                <h2>Contact Us</h2>
                <p class="subtitle">Have questions or feedback? We'd love to hear from you!</p>

                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="your_name">Your Name</label>
                        <input type="text" id="your_name" name="name" placeholder="Enter Your Name" required
                            value="{{ old('name', Auth::user()->name) }}">
                    </div>
                    <div class="form-group">
                        <label for="email_address">Email Address</label>
                        <input type="email" id="email_address" name="email" placeholder="Enter Your Email" required
                            value="{{ old('email', Auth::user()->email) }}">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your message here" required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn-submit">Send Message</button>
                </form>
            </div>

            <div class="contact-info-section">
                <h3>Get In Touch</h3>
                <div class="info-items">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Address</h4>
                            <p style="color:#fb0303">Jl. D.I. Panjaitan No. 128, Purwokerto, Banyumas, Jawa Tengah,
                                Indonesia</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.23 1.02l-2.2 2.2z" />
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Whatsapp Number</h4>
                            <p style="color:#fb0303">
                                <a href="https://wa.me/6287824253298" target="_blank"
                                    style="color:#fb0303; text-decoration:none;">
                                    087824253298
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                        </div>
                        <div class="info-text">
                            <h4>Email</h4>
                            <p><a href="https://mail.google.com/mail/?view=cm&fs=1&to=univenttelkom@gmail.com
&su=Pertanyaan%20Event&body=Halo%2C%20saya%20ingin%20bertanya%20tentang%20event..."
                                    target="_blank"
                                    style="color:#fb0303; text-decoration: underline;">univenttelkom@gmail.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
