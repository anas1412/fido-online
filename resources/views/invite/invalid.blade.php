<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation invalide</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen font-sans">
    <div class="bg-red-50 border-l-4 border-red-600 p-8 rounded-lg shadow-md max-w-lg w-full">
        <h1 class="text-2xl font-bold text-red-800 mb-2">Invitation invalide</h1>
        <p class="text-gray-800 mb-6 leading-relaxed">
            Le code d'invitation <span class="font-mono">{{ $code }}</span> est invalide, expiré ou déjà utilisé.
        </p>
        <a href="{{ url('/') }}"
           class="inline-block px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
            Retour à l'accueil
        </a>
    </div>
</body>
</html>
