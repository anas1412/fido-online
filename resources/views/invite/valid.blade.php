<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejoindre l'entreprise</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="bg-white border border-gray-200 p-8 rounded-xl shadow-lg max-w-lg w-full text-center">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            Invitation à rejoindre <span class="text-blue-600">{{ $tenant->name }}</span>
        </h1>
        <p class="text-gray-700 mb-6 leading-relaxed">
            Vous avez été invité à rejoindre cette entreprise avec votre compte actuel.  
            Souhaitez-vous accepter l'invitation ?
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <form action="{{ route('invite.join', ['code' => $code]) }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Rejoindre
                </button>
            </form>

            <a href="{{ url('/') }}"
               class="w-full px-6 py-3 bg-gray-300 text-gray-900 rounded-lg font-semibold hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition">
               Annuler
            </a>
        </div>
    </div>
</body>
</html>
