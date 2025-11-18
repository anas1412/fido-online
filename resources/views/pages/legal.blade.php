<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - {{ \App\Models\Setting::singleton()->site_name }}</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head>
<body class="bg-gray-50 text-gray-900 p-8">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-8">
        <div class="mb-6 flex justify-start">
            <img src="{{ asset('images/logo.png') }}" alt="Fido Logo" class="h-16 w-auto">
        </div>

        <!-- Updated color here -->
        <h1 class="text-3xl font-bold mb-6 text-[#6fbf44]">Mentions Légales</h1>
        
        <div class="prose max-w-none">
            {!! \App\Models\Setting::singleton()->legal_content ?? '<p>Contenu non défini.</p>' !!}
        </div>

        <div class="mt-8 pt-4 border-t text-center">
            <!-- Updated color here -->
            <a href="/" class="text-[#6fbf44] hover:underline">&larr; Retour</a>
        </div>
    </div>
</body>
</html>