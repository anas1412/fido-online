<x-filament-panels::page class="h-full">
    <div 
        x-data="{ 
            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.chatContainer;
                    container.scrollTop = container.scrollHeight;
                });
            }
        }"
        x-init="scrollToBottom(); Livewire.on('scroll-to-bottom', () => scrollToBottom())"
        class="flex flex-col h-[calc(100vh-12rem)] -mt-4" 
    >
        {{-- 
             NOTE: h-[calc(100vh-12rem)] is an approximation. 
             In Filament 4, you might want to wrap this in a absolute inset-0 container 
             if the page structure allows, but this is the safest responsive method.
        --}}

        {{-- Chat Area --}}
        <div 
            x-ref="chatContainer"
            class="flex-1 overflow-y-auto p-4 space-y-6 bg-gray-50 dark:bg-gray-900 rounded-t-xl border border-gray-200 dark:border-gray-700 shadow-inner"
        >
            @if(empty($this->chatHistory))
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <x-heroicon-o-chat-bubble-left-right class="w-16 h-16 mb-4 opacity-50"/>
                    <p>Commencez la conversation avec Fido...</p>
                </div>
            @endif

            @foreach ($this->chatHistory as $turn)
                @php
                    $isUser = $turn['role'] === 'user';
                    $messageText = $turn['parts'][0]['text'];
                @endphp

                <div class="flex w-full {{ $isUser ? 'justify-end' : 'justify-start' }}">
                    <div class="flex max-w-[80%] md:max-w-[70%] gap-3 {{ $isUser ? 'flex-row-reverse' : 'flex-row' }}">
                        
                        {{-- Avatar --}}
                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center {{ $isUser ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                            @if($isUser)
                                <span class="text-xs text-white font-bold">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                </span>
                            @else
                                <x-heroicon-m-cpu-chip class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                            @endif
                        </div>

                        {{-- Bubble --}}
                        <div @class([
                            'p-4 rounded-2xl shadow-sm text-sm leading-relaxed',
                            'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200' => !$isUser, // AI Style
                            'bg-primary-600 text-white' => $isUser, // User Style
                            'rounded-tl-none' => !$isUser, // Sharp corner for AI
                            'rounded-tr-none' => $isUser, // Sharp corner for User
                        ])>
                            {{-- User Message (Plain Text) --}}
                            @if($isUser)
                                <div class="whitespace-pre-wrap">{{ $messageText }}</div>
                            
                            {{-- AI Message (Markdown Rendered) --}}
                            @else
                                <div class="prose dark:prose-invert prose-sm max-w-none prose-p:m-0 prose-li:m-0">
                                    {!! Str::markdown($messageText) !!}
                                </div>
                            @endif

                            {{-- Timestamp (Optional) --}}
                            <div class="mt-1 text-[10px] opacity-70 {{ $isUser ? 'text-right text-primary-100' : 'text-left text-gray-400' }}">
                                {{ \Carbon\Carbon::parse($turn['timestamp'] ?? now())->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Typing Indicator --}}
            <div wire:loading wire:target="submitPrompt" class="flex justify-start w-full">
                <div class="flex items-center gap-3">
                     <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <x-heroicon-m-cpu-chip class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                    </div>
                    <div class="bg-gray-200 dark:bg-gray-800 p-3 rounded-2xl rounded-tl-none flex space-x-1">
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="bg-white dark:bg-gray-900 border-x border-b border-gray-200 dark:border-gray-700 rounded-b-xl p-4 z-10">
            <form wire:submit="submitPrompt" class="relative flex items-end gap-2">
                
                <div class="flex-grow"
                     x-data
                     {{-- Handle Enter to submit, Shift+Enter for newline --}}
                     @keydown.enter.prevent="if(!$event.shiftKey) { $wire.submitPrompt(); }"
                >
                    {{ $this->form }}
                </div>

                <x-filament::button
                    type="submit"
                    icon="heroicon-m-paper-airplane"
                    class="mb-1"
                    wire:loading.attr="disabled"
                    wire:target="submitPrompt"
                >
                    
                </x-filament::button>
            </form>
            <div class="text-center text-xs text-gray-400 mt-2">
                Fido peut faire des erreurs. Vérifiez les informations importantes.
            </div>
        </div>
    </div>
</x-filament-panels::page>