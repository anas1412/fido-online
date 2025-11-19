<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé - {{ config('app.name', 'Fido') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f4fcf1',
                            100: '#e2f7db',
                            500: '#6fbf44', // Sushi Green
                            600: '#5ea33a',
                            900: '#2d4f1e',
                        },
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full bg-slate-50 font-sans text-slate-600 antialiased selection:bg-brand-500 selection:text-white">

    <main class="grid min-h-full place-items-center px-6 py-24 sm:py-32 lg:px-8 relative isolate">
        
        <!-- Background Decoration Top -->
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-brand-100 to-brand-600 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>

        <div class="text-center relative z-10">
            <p class="text-base font-bold text-brand-600">403</p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">Accès refusé</h1>
            <p class="mt-6 text-base leading-7 text-slate-600">
                Désolé, vous n'avez pas la permission d'accéder à cette page.<br>
                Veuillez contacter votre administrateur si vous pensez qu'il s'agit d'une erreur.
            </p>
            
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <!-- Primary Action: Home -->
                <a href="/" class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 transition-all">
                    Retour à l'accueil
                </a>

                <!-- Secondary Action: Dashboard or Login -->
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-900 hover:text-brand-600 transition">
                        Tableau de bord <span aria-hidden="true">&rarr;</span>
                    </a>
                @else
                    <a href="{{ url('/dashboard/login') }}" class="text-sm font-semibold text-slate-900 hover:text-brand-600 transition">
                        Se connecter avec un autre compte
                    </a>
                @endauth
            </div>
        </div>

        <!-- Background Decoration Bottom -->
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#80caff] to-brand-500 opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>

    </main>

</body>
</html>