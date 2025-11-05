<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Landing Page</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 antialiased">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-32 text-center">
        <h1 class="text-5xl font-extrabold mb-4">Welcome to Our Platform</h1>
        <p class="text-xl mb-8">Consistent Filament styling outside the admin panel.</p>
        <x-filament::button color="secondary" size="lg" href="/register">
            Get Started
        </x-filament::button>
    </section>

    {{-- Features Section --}}
    <section class="py-24 max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        <x-filament::card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">Fast Setup</h2>
            </x-slot>
            <p class="mt-2 text-gray-600">Deploy your app quickly with Filament-inspired components.</p>
        </x-filament::card>

        <x-filament::card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">Beautiful UI</h2>
            </x-slot>
            <p class="mt-2 text-gray-600">Filament provides modern UI elements to maintain consistency.</p>
        </x-filament::card>

        <x-filament::card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">Extendable</h2>
            </x-slot>
            <p class="mt-2 text-gray-600">Easily add features without breaking the design language.</p>
        </x-filament::card>
    </section>

    {{-- Call to Action --}}
    <section class="py-20 bg-indigo-600 text-white text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to start?</h2>
        <p class="mb-6 text-lg">Sign up today and take advantage of Filament’s styling.</p>
        <x-filament::button href="/register" color="secondary" size="lg">
            Create Account
        </x-filament::button>
    </section>

</body>
</html>
