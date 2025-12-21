<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email Verification - Univent</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/verification.css') }}" />
    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">
</head>

<body>
    <header class="login-header">
        <a href="/"><img src="{{ asset('images/univent-logo.png') }}" alt="Univent Logo" class="logo"></a>
    </header>

    <main class="login-main">
        <div class="login-container">
            <h1>Email Verification</h1>

            <p class="verification-text">
                We've sent a verification code to your email:<br>
                <strong id="email-display" data-email="{{ $email ?? 'your.email@example.com' }}">
                    {{ $email ?? 'your.email@example.com' }}
                </strong>
            </p>

            <form action="{{ route('verification.verify') }}" method="POST" id="verify-form">
                @csrf
                <input type="hidden" name="email" value="{{ request()->query('email') }}">
                <input type="hidden" name="otp" id="otp-hidden-input">

                <div class="form-group">
                    <label for="code-1">Verification code</label>
                    <div class="code-inputs" id="otp-inputs">
                        <input type="text" id="code-1" name="code[]" maxlength="1" required data-index="0">
                        <input type="text" name="code[]" maxlength="1" required data-index="1">
                        <input type="text" name="code[]" maxlength="1" required data-index="2">
                        <input type="text" name="code[]" maxlength="1" required data-index="3">
                        <input type="text" name="code[]" maxlength="1" required data-index="4">
                        <input type="text" name="code[]" maxlength="1" required data-index="5">
                    </div>
                    <p class="helper-text">Enter the 6-digit code sent to your email.</p>
                </div>
                <button type="submit" class="btn btn-login">Verify</button>
            </form>

            <div class="resend-link">
                <span>Didn't receive the code?</span>
                <form action="{{ route('verification.resend') }}" method="POST" class="resend-form">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email ?? '' }}">
                    <button type="submit" class="resend-button">Resend Code</button>
                </form>
            </div>
            <p class="spam-note">
                Check your spam folder if you don't see the email.
            </p>
        </div>
    </main>

    @include('partials.sweetalert')

    <script src="{{ asset('js/verification.js') }}"></script>
</body>

</html>
