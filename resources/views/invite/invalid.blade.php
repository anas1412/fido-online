<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation invalide - {{ \App\Models\Setting::singleton()->site_name ?? 'Fido' }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans p-4">
    <div class="max-w-md w-full bg-white shadow-lg rounded-2xl p-8 text-center">
        
        <!-- Logo -->
        <div class="mb-8 flex justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto opacity-90 hover:opacity-100 transition">
        </div>

        <!-- Icon: Broken Link (Cleaner than an X) -->
        <div class="flex justify-center mb-6">
            <div class="bg-red-50 p-4 rounded-full">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
            </div>
        </div>

        <!-- Text Content -->
        <h1 class="text-2xl font-bold text-gray-900 mb-3">Lien expiré ou invalide</h1>
        
        <p class="text-gray-500 mb-8 leading-relaxed">
            Le code d'invitation <span class="font-mono text-gray-700 bg-gray-100 px-2 py-0.5 rounded text-sm mx-1">{{ $code }}</span> ne fonctionne pas. Il a peut-être expiré ou a déjà été utilisé.
        </p>

        <!-- Action Button -->
        <a href="{{ url('/') }}"
           class="inline-flex items-center justify-center w-full px-6 py-3 text-white font-semibold rounded-lg transition transform hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
           style="background-color: #6fbf44;">
            Retour à l'accueil
        </a>
        
        <div class="mt-6 text-sm text-gray-400">
            Besoin d'aide ? <a href="mailto:support@fido.com" class="hover:underline hover:text-gray-600">Contactez le support</a>
        </div>
    </div>
</body>
</html>