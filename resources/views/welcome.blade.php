<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Fido') }} - Gestion d'entreprise simplifiée</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    
    <!-- SEO -->
    <meta name="description" content="La solution idéale pour la gestion multi-organisations, la comptabilité et l'administration d'entreprise en Tunisie.">

    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        dark: {
                            800: '#1e293b', // Slate 800
                            900: '#0f172a', // Slate 900
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body x-data="{ mobileMenuOpen: false }" class="bg-slate-50 font-sans text-slate-600 antialiased selection:bg-brand-500 selection:text-white overflow-x-hidden">

    <!-- Navbar -->
    <header class="fixed top-0 z-40 w-full bg-white/90 backdrop-blur-md border-b border-slate-200">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            
            <!-- Logo Container -->
            <div class="flex lg:flex-1">
                <a href="/" class="-m-1.5 p-1.5 flex items-center gap-x-2">
                    <!-- Use your actual logo asset here -->
                    <img src="{{ asset('images/logo.png') }}" onerror="this.src='https://placehold.co/100x40/6fbf44/ffffff?text=FIDO'" alt="Fido Logo" class="h-8 w-auto">
                    <span class="font-bold text-xl text-slate-900 tracking-tight">{{ config('app.name', 'Fido') }}</span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex lg:gap-x-12">
                <a href="#features" class="text-sm font-medium leading-6 text-slate-900 hover:text-brand-600 transition">Fonctionnalités</a>
                <a href="#pricing" class="text-sm font-medium leading-6 text-slate-900 hover:text-brand-600 transition">Tarifs</a>
                <a href="#security" class="text-sm font-medium leading-6 text-slate-900 hover:text-brand-600 transition">Sécurité</a>
                <a href="#contact" class="text-sm font-medium leading-6 text-slate-900 hover:text-brand-600 transition">Contact</a>
            </div>

            <!-- Desktop CTA -->
            <div class="hidden lg:flex lg:flex-1 lg:justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold leading-6 text-slate-900 flex items-center gap-1">
                        Tableau de bord <span aria-hidden="true">&rarr;</span>
                    </a>
                @else
                    <a href="{{ url('/dashboard/login') }}" class="rounded-full bg-brand-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 transition-all">
                        Se connecter
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex lg:hidden">
                <button type="button" @click="mobileMenuOpen = true" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-slate-700 hover:bg-slate-100">
                    <span class="sr-only">Ouvrir le menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenuOpen" class="relative z-50 lg:hidden" x-cloak role="dialog" aria-modal="true">
            
        <!-- Backdrop -->
        <div x-show="mobileMenuOpen" 
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" 
                @click="mobileMenuOpen = false"></div>

        <!-- Sliding Panel -->
        <div class="fixed inset-0 z-50 flex">
            <div x-show="mobileMenuOpen" 
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative ml-auto flex h-full w-full max-w-sm flex-col overflow-y-auto bg-white py-4 pb-12 shadow-xl">
                
                <div class="flex items-center justify-between px-6">
                    <a href="#" class="-m-1.5 p-1.5 flex items-center gap-2">
                        <img src="{{ asset('images/logo.png') }}" onerror="this.src='https://placehold.co/100x40/6fbf44/ffffff?text=FIDO'" alt="Logo" class="h-8 w-auto">
                        <span class="font-bold text-lg text-slate-900">{{ config('app.name', 'Fido') }}</span>
                    </a>
                    <button type="button" @click="mobileMenuOpen = false" class="-m-2.5 rounded-md p-2.5 text-slate-700 hover:bg-slate-100">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-6 flow-root px-6">
                    <div class="-my-6 divide-y divide-slate-500/10">
                        <div class="space-y-2 py-6">
                            <a href="#features" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-slate-900 hover:bg-slate-50">Fonctionnalités</a>
                            <a href="#pricing" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-slate-900 hover:bg-slate-50">Tarifs</a>
                            <a href="#security" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-slate-900 hover:bg-slate-50">Sécurité</a>
                            <a href="#contact" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-slate-900 hover:bg-slate-50">Contact</a>
                        </div>
                        <div class="py-6">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-brand-600 hover:bg-slate-50">Accéder au Dashboard</a>
                            @else
                                <a href="{{ url('/dashboard/login') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-slate-900 hover:bg-slate-50">Se connecter</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main class="isolate">
        <!-- Hero Section -->
        <div class="relative pt-14 overflow-hidden">
            <!-- Background Blob 1 -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-brand-100 to-brand-600 opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>

            <div class="py-24 sm:py-32 lg:pb-40">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    
                    <!-- Grid Layout: Text Left, Image Right -->
                    <div class="grid grid-cols-1 gap-y-16 gap-x-8 lg:grid-cols-2 lg:items-center">
                        
                        <!-- Left Column: Text Content -->
                        <div class="mx-auto max-w-2xl lg:mx-0 text-center lg:text-left">
                            <div class="mb-6 flex justify-center lg:justify-start">
                                <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-slate-600 ring-1 ring-slate-900/10 hover:ring-slate-900/20 bg-white/50 backdrop-blur-sm">
                                    Nouveau : Gestion simplifiée des taux de TVA. <a href="#features" class="font-semibold text-brand-600"><span class="absolute inset-0" aria-hidden="true"></span>En savoir plus <span aria-hidden="true">&rarr;</span></a>
                                </div>
                            </div>
                            <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-6xl">
                                Gérez votre entreprise en toute <span class="text-brand-500">sérénité</span>.
                            </h1>
                            <p class="mt-6 text-lg leading-8 text-slate-600">
                                Fido est la solution tout-en-un pour la gestion multi-organisations, la comptabilité et la collaboration. Conçue spécifiquement pour les professionnels tunisiens.
                            </p>
                            <div class="mt-10 flex items-center justify-center lg:justify-start gap-x-6">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="rounded-md bg-brand-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 transition-all">Accéder à mon espace</a>
                                @else
                                    <a href="{{ url('/dashboard/login') }}" class="rounded-md bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-xl shadow-brand-500/20 hover:bg-brand-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 transition-all transform hover:-translate-y-1">Commencer maintenant</a>
                                    <a href="#features" class="text-sm font-semibold leading-6 text-slate-900">En savoir plus <span aria-hidden="true">→</span></a>
                                @endauth
                            </div>
                        </div>

                        <!-- Right Column: Screenshot / Image -->
                        <div class="relative mx-auto w-full max-w-lg lg:max-w-none">
                            <!-- Screenshot Container -->
                            <div class="relative rounded-xl bg-slate-900/5 p-2 ring-1 ring-inset ring-slate-900/10 lg:-m-4 lg:rounded-2xl lg:p-4 shadow-2xl">
                                <!-- Placeholder for Browser Top Bar -->
                                <div class="flex gap-2 mb-2 ml-2 lg:mb-4 lg:ml-1">
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-400"></div>
                                    <div class="h-2.5 w-2.5 rounded-full bg-yellow-400"></div>
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-400"></div>
                                </div>
                                
                                <!-- The Image: Replace src below with your dashboard screenshot -->
                                <img 
                                    src="https://placehold.co/1200x800/f8fafc/e2e8f0?text=Interface+Dashboard+Fido" 
                                    alt="Interface Dashboard Fido" 
                                    class="w-full rounded-md shadow-sm ring-1 ring-slate-900/10 bg-white"
                                    width="2432" 
                                    height="1442"
                                >
                                
                                <!-- Floating Decor Element (Optional) -->
                                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-xl border border-slate-100 hidden sm:block animate-bounce" style="animation-duration: 3s;">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-green-100 p-2 rounded-full text-green-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500">État</p>
                                            <p class="font-bold text-sm text-slate-800">À jour</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <!-- Background Blob 2 -->
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#80caff] to-brand-500 opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </div>

        <!-- Features Grid -->
        <div id="features" class="mx-auto mt-8 max-w-7xl px-6 sm:mt-16 lg:px-8 pb-24">
            <div class="mx-auto max-w-2xl lg:text-center">
                <h2 class="text-base font-semibold leading-7 text-brand-600">Tout-en-un</h2>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Tout ce dont votre entreprise a besoin</p>
                <p class="mt-6 text-lg leading-8 text-slate-600">
                    Une suite complète d'outils conçus pour l'efficacité, la sécurité et la simplicité. Adapté aux besoins des entreprises et comptables.
                </p>
            </div>
            
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="flex flex-col bg-white p-8 rounded-2xl shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition-shadow duration-300">
                        <dt class="flex items-center gap-x-3 text-base font-bold leading-7 text-slate-900">
                            <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            Multi-Organisations
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                            <p class="flex-auto">Gérez plusieurs environnements clients ou filiales avec une isolation stricte des données et des accès personnalisés.</p>
                        </dd>
                    </div>
                    <!-- Feature 2 -->
                    <div class="flex flex-col bg-white p-8 rounded-2xl shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition-shadow duration-300">
                        <dt class="flex items-center gap-x-3 text-base font-bold leading-7 text-slate-900">
                             <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                            </div>
                            Connexion Sécurisée
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                            <p class="flex-auto">Authentification simple et ultra-sécurisée via Google. Plus besoin de retenir des mots de passe complexes.</p>
                        </dd>
                    </div>
                    <!-- Feature 3 -->
                    <div class="flex flex-col bg-white p-8 rounded-2xl shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition-shadow duration-300">
                        <dt class="flex items-center gap-x-3 text-base font-bold leading-7 text-slate-900">
                             <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                            </div>
                            Administration
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                            <p class="flex-auto">Contrôle centralisé pour les administrateurs : gestion des utilisateurs, des invitations et des paramètres globaux.</p>
                        </dd>
                    </div>
                    <!-- Feature 4 -->
                    <div class="flex flex-col bg-white p-8 rounded-2xl shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition-shadow duration-300">
                        <dt class="flex items-center gap-x-3 text-base font-bold leading-7 text-slate-900">
                             <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                            </div>
                            Tableaux de Bord
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                            <p class="flex-auto">Des vues personnalisées offrant aux utilisateurs des données pertinentes et des outils spécifiques à leur dossier.</p>
                        </dd>
                    </div>
                    <!-- Feature 5 -->
                    <div class="flex flex-col bg-white p-8 rounded-2xl shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition-shadow duration-300">
                        <dt class="flex items-center gap-x-3 text-base font-bold leading-7 text-slate-900">
                             <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                            </div>
                            Onboarding Rapide
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                            <p class="flex-auto">Un processus simplifié permettant aux nouveaux utilisateurs de créer ou de rejoindre une organisation en quelques clics.</p>
                        </dd>
                    </div>
                    <!-- Feature 6 -->
                    <div class="flex flex-col bg-white p-8 rounded-2xl shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition-shadow duration-300">
                        <dt class="flex items-center gap-x-3 text-base font-bold leading-7 text-slate-900">
                             <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-brand-600">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                            </div>
                            Système d'invitation
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-slate-600">
                            <p class="flex-auto">Contrôlez l'accès aux organisations grâce à des codes d'invitation uniques et traçables pour chaque membre.</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Pricing Section -->
        <div id="pricing" class="bg-white py-24 sm:py-32 border-t border-slate-200">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl sm:text-center">
                    <h2 class="text-base font-semibold leading-7 text-brand-600">Tarification simple</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Des offres adaptées à vos besoins</p>
                    <p class="mt-6 text-lg leading-8 text-slate-600">Commencez gratuitement et évoluez au rythme de votre activité.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl rounded-3xl ring-1 ring-slate-200 sm:mt-20 lg:mx-0 lg:flex lg:max-w-none">
                    <div class="p-8 sm:p-10 lg:flex-auto">
                        <h3 class="text-2xl font-bold tracking-tight text-slate-900">Gratuit / Découverte</h3>
                        <p class="mt-6 text-base leading-7 text-slate-600">Idéal pour les freelances ou pour tester la plateforme sans engagement.</p>
                        <div class="mt-10 flex items-center gap-x-4">
                            <h4 class="flex-none text-sm font-semibold leading-6 text-brand-600">Inclus dans l'offre</h4>
                            <div class="h-px flex-auto bg-slate-100"></div>
                        </div>
                        <ul role="list" class="mt-8 grid grid-cols-1 gap-4 text-sm leading-6 text-slate-600 sm:grid-cols-2 sm:gap-6">
                            <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                1 organisation (Propriétaire)
                            </li>
                            <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                Rejoindre 2 organisations max
                            </li>
                            <li class="flex gap-x-3">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                30 Documents / an
                            </li>
                            <li class="flex gap-x-3 text-slate-400">
                                <svg class="h-6 w-5 flex-none text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                Support prioritaire
                            </li>
                        </ul>
                    </div>
                    <div class="-mt-2 p-2 lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0">
                        <div class="rounded-2xl bg-slate-50 py-10 text-center ring-1 ring-inset ring-slate-900/5 lg:flex lg:flex-col lg:justify-center lg:py-16">
                            <div class="mx-auto max-w-xs px-8">
                                <p class="text-base font-semibold text-slate-600">Abonnement Gratuit</p>
                                <p class="mt-6 flex items-baseline justify-center gap-x-2">
                                    <span class="text-5xl font-bold tracking-tight text-slate-900">0</span>
                                    <span class="text-sm font-semibold leading-6 tracking-wide text-slate-600">TND</span>
                                </p>
                                <a href="{{ url('/dashboard/login') }}" class="mt-10 block w-full rounded-md bg-slate-800 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 transition-all">Créer un compte</a>
                                <p class="mt-6 text-xs leading-5 text-slate-600">Aucune carte bancaire requise</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PRO PLAN -->
                <div class="mx-auto mt-8 max-w-2xl rounded-3xl ring-2 ring-brand-500 bg-slate-50 lg:mx-0 lg:flex lg:max-w-none shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-brand-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg z-10">RECOMMANDÉ</div>
                    <div class="p-8 sm:p-10 lg:flex-auto">
                        <h3 class="text-2xl font-bold tracking-tight text-brand-900">Offre Illimitée</h3>
                        <p class="mt-6 text-base leading-7 text-slate-600">Pour les entreprises en croissance et les cabinets comptables exigeants.</p>
                        <div class="mt-10 flex items-center gap-x-4">
                            <h4 class="flex-none text-sm font-semibold leading-6 text-brand-600">Tout inclus, sans limites</h4>
                            <div class="h-px flex-auto bg-brand-100"></div>
                        </div>
                        <ul role="list" class="mt-8 grid grid-cols-1 gap-4 text-sm leading-6 text-slate-600 sm:grid-cols-2 sm:gap-6">
                            <li class="flex gap-x-3 font-semibold text-brand-900">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                Organisations illimités
                            </li>
                            <li class="flex gap-x-3 font-semibold text-brand-900">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                Rejoindre illimitées
                            </li>
                            <li class="flex gap-x-3 font-semibold text-brand-900">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                Documents illimités
                            </li>
                            <li class="flex gap-x-3 font-semibold text-brand-900">
                                <svg class="h-6 w-5 flex-none text-brand-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                                Support prioritaire
                            </li>
                        </ul>
                    </div>
                    <div class="-mt-2 p-2 lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0">
                        <div class="rounded-2xl bg-brand-500 py-10 text-center ring-1 ring-inset ring-brand-600 lg:flex lg:flex-col lg:justify-center lg:py-16 h-full">
                            <div class="mx-auto max-w-xs px-8">
                                <p class="text-base font-semibold text-brand-100">Facturation Trimestrielle</p>
                                <p class="mt-6 flex items-baseline justify-center gap-x-2">
                                    <span class="text-5xl font-bold tracking-tight text-white">290</span>
                                    <span class="text-sm font-semibold leading-6 tracking-wide text-brand-100">TND</span>
                                </p>
                                <div class="mt-10 block w-full rounded-md bg-white/60 px-3 py-2 text-center text-sm font-semibold text-brand-800 shadow-sm cursor-not-allowed select-none">
                                    Bientôt disponible
                                </div>
                                <p class="mt-6 text-xs leading-5 text-brand-100">Facture avec TVA incluse</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trust / Security Section -->
        <div id="security" class="bg-dark-900 py-24 sm:py-32 relative isolate overflow-hidden">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:grid-cols-2 lg:items-center">
                    
                    <!-- Left Column: Text Content -->
                    <div class="px-6 lg:px-0 lg:pr-4 lg:pt-4">
                        <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-lg">
                            <h2 class="text-base font-semibold leading-7 text-brand-500">Sécurité avant tout</h2>
                            <p class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Vos données sont protégées</p>
                            <p class="mt-6 text-lg leading-8 text-slate-300">
                                Nous comprenons l'importance de vos données financières. C'est pourquoi Fido utilise une architecture isolée et sécurisée pour chaque organisation.
                            </p>
                            <dl class="mt-10 max-w-xl space-y-8 text-base leading-7 text-slate-300 lg:max-w-none">
                                <div class="relative pl-9">
                                    <dt class="inline font-semibold text-white">
                                        <svg class="absolute left-1 top-1 h-5 w-5 text-brand-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                        </svg>
                                        Chiffrement SSL.
                                    </dt>
                                    <dd class="inline">Toutes les communications sont chiffrées de bout en bout via HTTPS.</dd>
                                </div>
                                <div class="relative pl-9">
                                    <dt class="inline font-semibold text-white">
                                        <svg class="absolute left-1 top-1 h-5 w-5 text-brand-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 01-1.44-8.765 4.5 4.5 0 018.302-3.046 3.5 3.5 0 014.504 4.272A4 4 0 0115 17H5.5zm3.75-2.75a.75.75 0 001.5 0V9.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0l-3.25 3.5a.75.75 0 101.1 1.02l1.95-2.1v4.59z" clip-rule="evenodd" />
                                        </svg>
                                        Sauvegardes quotidiennes.
                                    </dt>
                                    <dd class="inline">Vos données sont sauvegardées automatiquement pour éviter toute perte.</dd>
                                </div>
                                <div class="relative pl-9">
                                    <dt class="inline font-semibold text-white">
                                        <svg class="absolute left-1 top-1 h-5 w-5 text-brand-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                                        </svg>
                                        Accès cloisonné.
                                    </dt>
                                    <dd class="inline">Séparation stricte entre les organisations pour une confidentialité totale.</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Right Column: Graphic Container -->
                    <div class="sm:px-6 lg:px-0">
                        <div class="relative isolate overflow-hidden bg-brand-500 px-6 pt-8 sm:mx-auto sm:max-w-2xl sm:rounded-3xl sm:pl-16 sm:pr-0 sm:pt-16 lg:mx-0 lg:max-w-none min-h-[500px] flex items-center justify-center">
                            
                            <!-- Background Abstract Effects -->
                            <div class="absolute -inset-y-px -left-3 -z-10 w-full origin-bottom-left skew-x-[-30deg] bg-brand-100 opacity-20 ring-1 ring-inset ring-white" aria-hidden="true"></div>
                            <div class="absolute inset-0 -z-10 bg-[radial-gradient(#ffffff33_1px,transparent_1px)] [background-size:16px_16px] opacity-20"></div>

                            <!-- Main Visual: Technical Security Status -->
                            <div class="mx-auto max-w-2xl sm:mx-0 sm:max-w-none w-full relative z-10">
                                
                                <!-- The Main Card -->
                                <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-gray-900/10 overflow-hidden">
                                    
                                    <!-- Card Header: Live Status -->
                                    <div class="border-b border-slate-100 bg-slate-50 p-4 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                            </span>
                                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">État du système</span>
                                        </div>
                                        <div class="flex gap-1">
                                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                                            <div class="h-2 w-2 rounded-full bg-slate-300"></div>
                                        </div>
                                    </div>

                                    <!-- Card Body: Honest Features -->
                                    <div class="p-6 space-y-5">
                                        
                                        <!-- Hero Status -->
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="h-12 w-12 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center border border-brand-100">
                                                <!-- Shield Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900">Protection Active</h3>
                                                <p class="text-xs text-slate-500">Vos données sont privées et sécurisées.</p>
                                            </div>
                                        </div>

                                        <!-- Security Features List -->
                                        <div class="space-y-3">
                                            
                                            <!-- Item 1: Connection Security -->
                                            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100">
                                                <div class="mt-0.5 text-brand-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">Connexion HTTPS / SSL</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Toutes les communications sont cryptées.</p>
                                                </div>
                                            </div>

                                            <!-- Item 2: Isolation -->
                                            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100">
                                                <div class="mt-0.5 text-brand-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">Isolation des données</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Informations strictement cloisonnées.</p>
                                                </div>
                                            </div>

                                            <!-- Item 3: Backups -->
                                            <div class="flex items-start gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100">
                                                <div class="mt-0.5 text-brand-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">Sauvegardes Régulières</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Vos données sont sauvegardées auto.</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Floating Badge 1: Auth -->
                                <div class="absolute -top-4 -right-4 bg-dark-900 rounded-lg p-3 shadow-xl border border-slate-700 flex items-center gap-3 transform rotate-3">
                                    <div class="bg-slate-700 p-1.5 rounded text-white">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-slate-400 leading-tight">Identification</div>
                                        <div class="text-xs font-bold text-white">Accès Sécurisé</div>
                                    </div>
                                </div>

                                <!-- Floating Badge 2: Uptime -->
                                <div class="absolute -bottom-5 -left-4 bg-white rounded-lg p-3 shadow-xl ring-1 ring-slate-200 flex items-center gap-3 transform -rotate-2">
                                    <div class="bg-green-100 p-1.5 rounded text-green-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[10px] text-slate-500 leading-tight">Disponibilité</div>
                                        <div class="text-xs font-bold text-slate-900">99.9% Uptime</div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action (CTA) Section -->
        <div class="bg-brand-600 relative isolate overflow-hidden">
            <div class="px-6 py-24 sm:px-6 sm:py-32 lg:px-8">
                <div class="mx-auto max-w-2xl text-center relative z-10">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Prêt à transformer votre gestion ?<br>
                        Commencez dès aujourd'hui.
                    </h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-brand-100">
                        Rejoignez les entreprises qui font confiance à Fido pour simplifier leur comptabilité et leur gestion quotidienne.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-brand-600 shadow-sm hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-all">
                                Accéder à mon espace
                            </a>
                        @else
                            <a href="{{ url('/dashboard/login') }}" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-brand-600 shadow-sm hover:bg-brand-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white transition-all">
                                Commencer gratuitement
                            </a>
                            <a href="#contact" class="text-sm font-semibold leading-6 text-white">Contactez-nous <span aria-hidden="true">→</span></a>
                        @endauth
                    </div>
                </div>
                <!-- Decorative background effect for CTA -->
                <svg viewBox="0 0 1024 1024" class="absolute left-1/2 top-1/2 -z-10 h-[64rem] w-[64rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]" aria-hidden="true">
                    <circle cx="512" cy="512" r="512" fill="url(#827591b1-ce8c-4110-b064-7cb85a0b1217)" fill-opacity="0.7" />
                    <defs>
                    <radialGradient id="827591b1-ce8c-4110-b064-7cb85a0b1217">
                        <stop stop-color="#ffffff" />
                        <stop offset="1" stop-color="#6fbf44" />
                    </radialGradient>
                    </defs>
                </svg>
            </div>
        </div>
        
    </main>

    <!-- Footer -->
    <footer id="contact" class="bg-white border-t border-slate-200">
        <div class="mx-auto max-w-7xl overflow-hidden px-6 py-20 sm:py-24 lg:px-8">
            <div class="text-center mb-10">
                <h3 class="text-xl font-bold text-slate-900">Une question ?</h3>
                <p class="mt-2 text-slate-600">Notre équipe support est là pour vous aider.</p>
                <a href="mailto:support@fido.tn" class="mt-4 inline-block text-brand-600 font-semibold hover:text-brand-500 transition">support@fido.tn</a>
            </div>

            <!-- Quick Links -->
            <nav class="-mb-6 columns-2 sm:flex sm:justify-center sm:space-x-12" aria-label="Footer">
                <div class="pb-6">
                    <a href="{{ route('about') }}" class="text-sm leading-6 text-slate-600 hover:text-slate-900">À propos</a>
                </div>
                <div class="pb-6">
                    <a href="{{ route('legal') }}" class="text-sm leading-6 text-slate-600 hover:text-slate-900">Mentions légales</a>
                </div>
                <div class="pb-6">
                    <a href="{{ route('privacy-policy') }}" class="text-sm leading-6 text-slate-600 hover:text-slate-900">Politique de confidentialité</a>
                </div>
            </nav>

            <p class="mt-10 text-center text-xs leading-5 text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'Fido') }}. Tous droits réservés.
            </p>
        </div>
    </footer>

</body>
</html>