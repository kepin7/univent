<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password - Univent</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    <link rel="icon" href="{{ asset('images/univent-logo3.png') }}" type="image/png">
</head>
<body>
    <header class="login-header">
        <a href="/"><img src="{{ asset('images/univent-logo.png') }}" alt="Univent Logo" class="logo"></a>
    </header>
    <main class="login-main">
        <div class="login-container">
            <h1>Forgot Password</h1>
            <p class="signup-link">Masukkan email Anda untuk menerima kode reset.</p>
            
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <button type="submit" class="btn btn-login">Send Password Reset Code</button>
            </form>
            
            <a href="{{ route('login') }}" class="forgot-password" style="text-align: center; margin-top: 2rem;">Back to Login</a>
        </div>
    </main>
    @include('partials.sweetalert') </body>
</html>