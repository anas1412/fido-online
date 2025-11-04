<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="scroll-behavior: smooth;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Fido Online') }} - Streamlined Business Solutions</title>

    <!-- Meta Tags for SEO -->
    <meta name="description" content="Fido Online offers an intuitive platform for multi-tenant management, secure Google authentication, and comprehensive business oversight.">
    <meta name="keywords" content="multi-tenant, saas, business solutions, google authentication, admin panel, fido online">

    <!-- Favicon -->
    {{-- <link rel="icon" href="/favicon.ico" type="image/x-icon"> --}}

    <!-- Scripts & Styles (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        // Define custom color palette for Tailwind CSS CDN
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#10B981', // Professional Green (Emerald)
                            dark: '#059669'    // Darker Emerald
                        },
                        secondary: {
                            DEFAULT: '#2C3E50', // Dark Slate Gray
                            light: '#34495E'   // Lighter Slate Gray
                        },
                        light: '#ECF0F1',     // Light Gray BG
                        muted: '#7F8C8D'     // Grayish Blue Text
                    }
                }
            }
        }
    </script>
    <style>
        /* Smooth scroll margin for fixed header */
        [id] {
            scroll-margin-top: 80px;
        }
    </style>
</head>

<body x-data="{ isMenuOpen: false }" class="bg-gray-50 font-sans text-secondary antialiased">

    <!-- Header -->
    <header class="fixed z-50 w-full bg-white/80 backdrop-blur-sm shadow-md transition-all">
        <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center text-2xl font-bold text-secondary">
                    <svg class="h-8 w-8 text-primary mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                    <span>{{ config('app.name', 'Fido Online') }}</span>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden items-center space-x-8 md:flex">
                    <a href="#features" class="text-muted hover:text-primary transition-colors duration-200">Features</a>
                    <a href="#contact" class="text-muted hover:text-primary transition-colors duration-200">Contact</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="ml-4 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-primary-dark transition-all duration-200">Dashboard</a>
                    @else
                        <a href="{{ url('/dashboard/login') }}" class="ml-4 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-primary-dark transition-all duration-200">Login</a>
                    @endauth
                </nav>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="isMenuOpen = !isMenuOpen" aria-label="Open Menu">
                        <svg class="h-7 w-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            <path x-show="isMenuOpen" style="display: none;" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="isMenuOpen" x-transition @click.away="isMenuOpen = false" class="absolute w-full bg-white shadow-lg md:hidden" style="display: none;">
            <div class="flex flex-col space-y-4 px-4 pt-2 pb-5">
                <a href="#features" @click="isMenuOpen = false" class="block rounded px-3 py-2 text-muted hover:bg-light hover:text-primary">Features</a>
                <a href="#contact" @click="isMenuOpen = false" class="block rounded px-3 py-2 text-muted hover:bg-light hover:text-primary">Contact</a>
                <div class="border-t border-gray-200 pt-4">
                     @auth
                        <a href="{{ url('/dashboard') }}" class="block w-full text-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-primary-dark transition-all duration-200">Dashboard</a>
                    @else
                        <a href="{{ url('/dashboard/login') }}" class="block w-full text-center rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-primary-dark transition-all duration-200">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative bg-secondary pt-32 pb-20 text-white md:pt-40 md:pb-28">
            <div class="absolute inset-0 bg-secondary-light opacity-20 [mask-image:radial-gradient(ellipse_at_center,transparent_20%,black)]"></div>
            <div class="container relative mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                    Streamlined Solutions for Modern Businesses
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-gray-300">
                    Fido Online offers an intuitive platform for multi-tenant management, secure Google authentication, and comprehensive business oversight.
                </p>
                <div class="mt-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="transform rounded-lg bg-primary px-8 py-4 text-lg font-semibold text-white shadow-xl transition-transform duration-200 hover:-translate-y-1 hover:bg-primary-dark">
                            Access Dashboard
                        </a>
                    @else
                        <a href="{{ url('/dashboard/login') }}" class="transform rounded-lg bg-primary px-8 py-4 text-lg font-semibold text-white shadow-xl transition-transform duration-200 hover:-translate-y-1 hover:bg-primary-dark">
                            Start Your Journey
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 sm:py-28">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold tracking-tight text-secondary sm:text-4xl">
                        Everything Your Business Needs
                    </h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-muted">
                        A complete suite of tools designed for scalability, security, and simplicity.
                    </p>
                </div>

                <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature Card: Multi-Tenancy -->
                    <div class="transform rounded-xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-primary">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary">Multi-Tenancy</h3>
                        <p class="mt-2 text-muted">Manage diverse client environments with robust data isolation and tailored access controls.</p>
                    </div>

                    <!-- Feature Card: Google Auth -->
                    <div class="transform rounded-xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary">Google Auth</h3>
                        <p class="mt-2 text-muted">Secure and effortless login experience, leveraging Google's trusted authentication.</p>
                    </div>
                    
                    <!-- Feature Card: Admin Panel -->
                    <div class="transform rounded-xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary">Admin Panel</h3>
                        <p class="mt-2 text-muted">Centralized control for administrators to manage tenants, users, and invitations efficiently.</p>
                    </div>
                    
                    <!-- Feature Card: User Dashboards -->
                    <div class="transform rounded-xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary">User Dashboards</h3>
                        <p class="mt-2 text-muted">Personalized dashboards providing users with relevant, tenant-specific data and tools.</p>
                    </div>

                    <!-- Feature Card: Onboarding -->
                    <div class="transform rounded-xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary">Onboarding</h3>
                        <p class="mt-2 text-muted">Streamlined process for new users to create or join existing tenants with ease.</p>
                    </div>
                    
                    <!-- Feature Card: Invite System -->
                    <div class="transform rounded-xl border border-gray-200 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-primary">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary">Invite System</h3>
                        <p class="mt-2 text-muted">Controlled user access through unique, trackable invitation codes for each tenant.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer id="contact" class="bg-secondary">
        <div class="container mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="text-center text-gray-400">
                <p class="mb-4 text-lg">Have questions? We'd love to hear from you.</p>
                <a href="mailto:support@fido.online" class="text-primary hover:text-primary-dark text-xl font-semibold transition-colors duration-200">
                    support@fido.online
                </a>
                <p class="mt-8 text-sm">&copy; {{ date('Y') }} {{ config('app.name', 'Fido Online') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>