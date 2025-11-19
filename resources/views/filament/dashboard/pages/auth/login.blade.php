<div class="flex min-h-screen bg-white font-sans text-slate-600 selection:bg-[#6fbf44] selection:text-white">
    
    {{-- LEFT COLUMN: Branding & Creative (Hidden on Mobile) --}}
    <div class="relative hidden w-0 flex-1 lg:block bg-slate-900 overflow-hidden">
        
        {{-- Background Blobs (From your Landing Page) --}}
        <div class="absolute inset-0 overflow-hidden">
             <div class="absolute -top-[20%] -left-[10%] aspect-[1155/678] w-[72rem] rotate-[30deg] bg-gradient-to-tr from-[#e2f7db] to-[#6fbf44] opacity-20" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
             <div class="absolute bottom-0 right-0 aspect-[1155/678] w-[72rem] -translate-x-1/2 bg-gradient-to-tr from-[#80caff] to-[#6fbf44] opacity-20" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>

        {{-- Content Overlay --}}
        <div class="relative flex h-full flex-col justify-center px-12 py-12 text-white z-10">
            
            {{-- The "Security Card" Visual (Extracted from your landing page) --}}
            <div class="mx-auto w-full max-w-md transform transition-all hover:scale-105 duration-500">
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl ring-1 ring-gray-900/10 overflow-hidden">
                    <div class="border-b border-slate-100 bg-slate-50 p-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#6fbf44] opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-[#5ea33a]"></span>
                            </span>
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Connexion Sécurisée</span>
                        </div>
                    </div>
                    <div class="p-6">
                         <div class="flex items-center gap-4 mb-4">
                            <div class="h-12 w-12 rounded-full bg-[#f4fcf1] text-[#6fbf44] flex items-center justify-center border border-[#e2f7db]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Espace Membre</h3>
                                <p class="text-xs text-slate-500">Accès chiffré de bout en bout.</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                             <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                 <div class="h-full bg-[#6fbf44] w-3/4 rounded-full animate-pulse"></div>
                             </div>
                             <div class="flex justify-between text-xs text-slate-400">
                                 <span>Vérification</span>
                                 <span>En attente...</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white">Gérez votre entreprise</h2>
                <p class="mt-4 text-lg text-slate-300">Connectez-vous pour accéder à vos organisations, vos documents et votre comptabilité.</p>
            </div>
            
            <div class="absolute bottom-10 left-0 right-0 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN: Form --}}
    <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            
            {{-- Logo --}}
            <div class="mb-10 flex items-center gap-x-2">
                 <img src="{{ asset('images/logo.png') }}" 
                      onerror="this.src='https://placehold.co/100x40/6fbf44/ffffff?text=FIDO'" 
                      alt="Fido" class="h-10 w-auto">
                 <span class="font-bold text-2xl text-slate-900 tracking-tight">{{ config('app.name', 'Fido') }}</span>
            </div>

            <div>
                <h2 class="text-2xl font-bold leading-9 tracking-tight text-slate-900">
                    Ravi de vous revoir
                </h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Veuillez vous identifier pour continuer.
                </p>
            </div>

            <div class="mt-10">
                <div>
                    {{-- The Google Button --}}
                    <x-google-login-button />
                </div>

                <div class="mt-10">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm font-medium leading-6">
                            <span class="bg-white px-6 text-slate-400">Besoin d'aide ?</span>
                        </div>
                    </div>

                    {{-- Alpine Context for Modal --}}
<div x-data="{ helpModalOpen: false }">

    {{-- BUTTONS --}}
    <div class="mt-6 grid grid-cols-2 gap-4">
        {{-- Button 1: Support (Opens Modal) --}}
        <button type="button" 
                @click="helpModalOpen = true"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-100 hover:text-brand-600 transition-all">
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
            </svg>
            <span>Aide & Support</span>
        </button>

        {{-- Button 2: Contact (Direct Email) --}}
        <a href="mailto:support@fido.tn?subject=Problème de connexion - Fido Dashboard" 
           class="flex w-full items-center justify-center gap-2 rounded-lg bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-100 hover:text-brand-600 transition-all">
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
            <span>Nous contacter</span>
        </a>
    </div>

    {{-- MODAL OVERLAY --}}
    <div x-show="helpModalOpen" 
         style="display: none;"
         class="relative z-50" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        {{-- Backdrop --}}
        <div x-show="helpModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity"></div>

        {{-- Modal Panel --}}
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="helpModalOpen"
                     @click.away="helpModalOpen = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-100">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-brand-50 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">Centre d'aide Fido</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500">
                                        Vous rencontrez des difficultés pour vous connecter ? Voici comment nous joindre :
                                    </p>

                                    {{-- Contact List --}}
                                    <div class="mt-4 space-y-3">
                                        {{-- Phone / WhatsApp --}}
                                        <a href="https://wa.me/21600000000" target="_blank" class="flex items-center gap-3 rounded-md p-2 hover:bg-slate-50 transition">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-600">
                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900">WhatsApp</div>
                                                <div class="text-xs text-slate-500">+216 50 377 851</div>
                                            </div>
                                        </a>

                                        {{-- Email --}}
                                        <a href="mailto:support@fido.tn" class="flex items-center gap-3 rounded-md p-2 hover:bg-slate-50 transition">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-slate-900">Email</div>
                                                <div class="text-xs text-slate-500">support@fido.tn</div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" 
                                @click="helpModalOpen = false"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                    
                    {{-- Empty Form render to keep Filament happy --}}
                    <div class="hidden">
                        {{ $this->form }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>