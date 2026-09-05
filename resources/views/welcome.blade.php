<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTM Rental Fraud Detection</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 font-sans bg-cover bg-center bg-no-repeat relative" 
      style="background-image: url('{{ asset('images/uptm3.jpg') }}');">

    <!-- Subtle Blur Background Overlay matching reference style -->
    <div class="absolute inset-0 bg-black/20 backdrop-blur-[1px]"></div>

    <!-- Main Content Box (Styled to match UiTM Reference Card: Square corners, sharp layout, navy typography) -->
    <div class="bg-white max-w-xl w-full px-8 py-12 sm:px-14 sm:py-16 text-center shadow-2xl relative z-10">

        <!-- UPTM Logo & System Title Block -->
        <div class="mb-6 flex flex-col items-center">
            <!-- Centered Logo Above Title -->
            <img src="{{ asset('images/LOGO_UPTM.png') }}" 
                 alt="UPTM Logo" 
                 class="h-24 w-auto object-contain mb-6">

            <!-- Bold Navy Title (Matched to "SELAMAT DATANG KE NRHOME") -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0A1128] tracking-wide uppercase">
                UPTM Rental Fraud Detection
            </h1>
            <!--<p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mt-2">
                Universiti Poly-Tech Malaysia Secure Portal-->
            </p>
        </div>

        <!-- Description -->
        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mb-8 px-4">
            Only verified UPTM students and MPP members can access the platform. All listings are checked to help prevent fake rentals.
        </p>

        <!-- Dynamic Session Flash Message / Unauthorized Access Error -->
        @if (session('error') || $errors->any())
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-8 text-left flex items-start space-x-3">
                <div class="text-red-500 text-base mt-0.5 shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h2 class="text-xs font-bold text-red-600">Unauthorized Account</h2>
                    <p class="text-xs text-red-500 mt-1 leading-normal">
                        {{ session('error') ?? $errors->first() ?? 'Please login with your official @student.uptm.edu.my email.' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Google Login Button (Styled with the Dark Navy color scheme) -->
        <div class="flex justify-center mb-8">
            <a href="{{ route('google.login') }}" class="w-full max-w-sm inline-flex items-center justify-center gap-3 px-5 py-2.5 bg-white text-slate-700 font-semibold text-sm border border-slate-300 rounded-lg hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-200 transition duration-150 shadow-xs">
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Continue with Google</span>
            </a>
        </div>

        <!-- Footer Security Info (Styled like reference footer text) -->
        <div class="pt-4 text-xs text-gray-700">
            <p>Access restricted to <span class="font-bold text-[#0A1128]">@student.uptm.edu.my</span> and <span class="font-bold text-[#0A1128]">@mpp.uptm.edu.my</span></p>
        </div>

    </div>

</body>

@if(session('error'))

    <div id="session-alert"
         class="fixed top-5 left-1/2 z-[9999] w-[90%] max-w-md -translate-x-1/2 rounded-md border border-amber-200 bg-amber-50 px-5 py-4 shadow-lg">

        <div class="flex items-start gap-3">

            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="flex-1">

                <h3 class="text-sm font-bold text-amber-800">
                    Session Expired
                </h3>

                <p class="mt-1 text-xs text-amber-700">
                    {{ session('error') }}
                </p>

            </div>

            <button type="button"
                    onclick="document.getElementById('session-alert').remove()"
                    class="text-amber-500 hover:text-amber-700">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>

    </div>

    <script>
        setTimeout(() => {
            const alert = document.getElementById('session-alert');

            if (alert) {
                alert.remove();
            }
        }, 5000);
    </script>

@endif
</html>