<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up - Univent</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">
</head>

<body>
    <header class="login-header">
        <a href="/"><img src="{{ asset('images/univent-logo.png') }}" alt="Univent Logo" class="logo"></a>
    </header>
    <main class="login-main">
        <div class="login-container">
            <h1>Sign Up</h1>
            <p class="signup-link">Already have an account? <a href="{{ route('login') }}">Log In</a></p>

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <svg class="password-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                        <svg class="password-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </div>
                </div>
                <button type="submit" class="btn btn-login">Continue</button>
            </form>
            <div class="divider">
                <span></span>
                <p>Or</p><span></span>
            </div>
            <a href="{{ route('google.redirect') }}" class="btn btn-google">
                <svg xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" viewBox="0 0 256 262">
                    <path fill="#4285F4"
                        d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.686H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027">
                    </path>
                    <path fill="#34A853"
                        d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1">
                    </path>
                    <path fill="#FBBC05"
                        d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782">
                    </path>
                    <path fill="#EB4335"
                        d="M130.55 50.479c14.931 0 27.223 5.17 35.82 13.44l36.824-36.824C180.452 12.511 157.626 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.782c10.59-31.477 39.894-54.25 74.414-54.25">
                    </path>
                </svg>
                <span>Continue with Google</span>
            </a>
        </div>
    </main>

    @include('partials.sweetalert')

    {{-- Hapus skrip inline dan ganti dengan ini --}}
    <script src="{{ asset('js/password-toggle.js') }}"></script>
</body>

</html>
