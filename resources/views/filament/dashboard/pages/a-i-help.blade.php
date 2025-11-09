<x-filament-panels::page>
    <div
        x-data="{
            init() {
                Livewire.on('scroll-to-bottom', () => {
                    this.$nextTick(() => {
                        const chatContainer = this.$refs.chatContainer;
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    });
                });
            }
        }"
        class="h-screen flex flex-col space-y-2"
    >
        {{-- Chat History --}}
        <div 
            class="flex-grow overflow-y-auto p-4 space-y-4"
            style="max-height: calc(100vh - 350px);" {{-- adjust to make chat shorter/taller --}}
            x-ref="chatContainer"
        >
            @php
                $converter = new \League\CommonMark\CommonMarkConverter();
            @endphp

            @foreach ($this->chatHistory as $index => $turn)
                @if ($turn['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="bg-primary-500 text-white p-3 rounded-lg max-w-md">
                            <strong>Vous:</strong> {{ $turn['parts'][0]['text'] }}
                        </div>
                    </div>
                @elseif ($turn['role'] === 'model')
                    <div class="flex justify-start">
                        <div class="bg-gray-200 dark:bg-gray-700 p-3 rounded-lg max-w-md">
                            <strong>Fido:</strong>
                            @if ($index === count($this->chatHistory) - 1 && $this->response)
                                {!! $converter->convertToHtml($this->response) !!}
                            @else
                                {!! $converter->convertToHtml($turn['parts'][0]['text']) !!}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Form --}}
        <form wire:submit="submitPrompt" class="p-4 border-t flex-shrink-0 space-y-2 bg-white dark:bg-gray-800">
            {{ $this->form }}
            <div class="flex justify-between">
                <x-filament::button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submitPrompt"
                >
                    <span wire:loading.remove wire:target="submitPrompt">Envoyer</span>
                    <span wire:loading wire:target="submitPrompt">Chargement...</span>
                </x-filament::button>

                <x-filament::button
                    type="button"
                    wire:click="clearConversation"
                    color="danger"
                    outlined
                >
                    Effacer la Conversation
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
