<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - Univent</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    {{-- 1. Tambahkan CSS untuk kotak-kotak OTP --}}
    <link rel="stylesheet" href="{{ asset('css/verification.css') }}" />
    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">
</head>

<body>
    <header class="login-header">
        <a href="/"><img src="{{ asset('images/univent-logo.png') }}" alt="Univent Logo" class="logo"></a>
    </header>
    <main class="login-main">
        <div class="login-container">
            <h1>Reset Your Password</h1>
            <p class="verification-text" style="text-align: center;">Cek email Anda untuk kode OTP.</p>

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                {{-- 2. Ganti input OTP tunggal dengan 6 kotak --}}
                <div class="form-group">
                    <label for="code-1">Kode OTP</label>
                    <input type="hidden" name="otp" id="otp-hidden-input">
                    <div class="code-inputs" id="otp-inputs">
                        <input type="text" id="code-1" name="code[]" maxlength="1" required data-index="0">
                        <input type="text" name="code[]" maxlength="1" required data-index="1">
                        <input type="text" name="code[]" maxlength="1" required data-index="2">
                        <input type="text" name="code[]" maxlength="1" required data-index="3">
                        <input type="text" name="code[]" maxlength="1" required data-index="4">
                        <input type="text" name="code[]" maxlength="1" required data-index="5">
                    </div>
                    <p class="helper-text">Masukkan 6-digit kode OTP.</p>
                </div>

                {{-- 3. Tambahkan wrapper dan ikon untuk hide/unhide password --}}
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <svg class="password-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                        <svg class="password-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">Change Password</button>
            </form>
        </div>
    </main>

    @include('partials.sweetalert')

    {{-- 4. Muat file JS eksternal --}}
    <script src="{{ asset('js/verification.js') }}"></script>
    <script src="{{ asset('js/password-toggle.js') }}"></script>
</body>

</html>
