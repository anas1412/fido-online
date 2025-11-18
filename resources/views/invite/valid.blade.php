<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejoindre l'entreprise - {{ \App\Models\Setting::singleton()->site_name ?? 'Fido' }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans p-4">
    <div class="max-w-md w-full bg-white shadow-lg rounded-2xl p-8 text-center">
        
        <!-- Logo -->
        <div class="mb-8 flex justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto opacity-90 hover:opacity-100 transition">
        </div>

        <!-- Icon: Office/Company (Welcoming Green) -->
        <div class="flex justify-center mb-6">
            <!-- Using brand color with 10% opacity for background -->
            <div class="p-4 rounded-full" style="background-color: rgba(111, 191, 68, 0.1);">
                <svg class="w-10 h-10" style="color: #6fbf44;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Invitation reçue</h1>
        
        <p class="text-gray-500 mb-6 leading-relaxed">
            Vous avez été invité à rejoindre l'espace de travail :
        </p>

        <!-- Tenant Name Highlight -->
        <div class="bg-gray-50 rounded-xl p-4 mb-8 border border-gray-100">
            <span class="block text-sm text-gray-400 uppercase tracking-wider font-semibold">Entreprise</span>
            <span class="block text-xl font-bold mt-1" style="color: #6fbf44;">
                {{ $tenant->name }}
            </span>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <form action="{{ route('invite.join', ['code' => $code]) }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-3 text-white font-semibold rounded-lg transition transform hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    style="background-color: #6fbf44;">
                    Accepter et rejoindre
                </button>
            </form>

            <a href="{{ url('/') }}"
               class="block w-full px-6 py-3 text-gray-500 font-medium rounded-lg hover:bg-gray-50 hover:text-gray-700 transition">
               Non, annuler
            </a>
        </div>

    </div>
</body>
</html>