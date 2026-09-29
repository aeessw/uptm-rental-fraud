<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTM Rental Fraud Detection</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
</head>
<body class="welcome-page"
      style="background-image: url('{{ asset('images/uptm3.jpg') }}');">

    <!-- Subtle Blur Background Overlay matching reference style -->
    <div class="welcome-overlay"></div>

    <!-- Main Content Box (Styled to match UiTM Reference Card: Square corners, sharp layout, navy typography) -->
    <div class="welcome-card">

        <!-- UPTM Logo & System Title Block -->
        <div class="welcome-heading">
            <!-- Centered Logo Above Title -->
            <img src="{{ asset('images/LOGO_UPTM.png') }}"
                 alt="UPTM Logo" height="86"
                 class="welcome-logo">

            <!-- Montserrat Black system title -->
            <h2 class="welcome-title">
                UPTM Rental Fraud Detection
            </h2>
        </div>

        <!-- Description -->
        <p class="welcome-description">
            Only verified UPTM students and MPP members can access the platform. All listings are checked to help prevent fake rentals.
        </p>

        <!-- Dynamic Session Flash Message / Unauthorized Access Error -->
        @if (session('error') || $errors->any())
            <div class="welcome-error" role="alert">
                <div class="welcome-error-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h2 class="welcome-error-title">Unauthorized Account</h2>
                    <p class="welcome-error-message">
                        {{ session('error') ?? $errors->first() ?? 'Please login with your official @student.uptm.edu.my email.' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Google Login Button (Styled with the Dark Navy color scheme) -->
        <div class="welcome-login">
            <a href="{{ route('google.login') }}" class="google-login">
                <svg width="20" height="20" aria-hidden="true" class="google-logo" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Continue with Google</span>
            </a>
        </div>

        <!-- Footer Security Info (Styled like reference footer text) -->
        <div class="welcome-footer">
            <p>Access restricted to <span class="welcome-domain">@student.uptm.edu.my</span> and <span class="welcome-domain">@mpp.uptm.edu.my</span></p>
        </div>

    </div>


</body>
</html>
