<x-filament-panels::page class="h-full">
    {{-- 1. Include the Markdown Parser --}}
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <div 
        x-data="aiChat()"
        x-init="initChat()"
        class="flex flex-col h-[calc(100vh-12rem)] -mt-4" 
    >
        {{-- Chat Area --}}
        <div 
            x-ref="chatContainer"
            class="flex-1 overflow-y-auto p-4 space-y-6 bg-gray-50 dark:bg-gray-900 rounded-t-xl border border-gray-200 dark:border-gray-700 shadow-inner"
        >
            <template x-if="history.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-gray-400 opacity-60">
                    <x-heroicon-o-chat-bubble-left-right class="w-16 h-16 mb-4"/>
                    <p>Fido est prêt. Posez une question sur vos données.</p>
                </div>
            </template>

            <template x-for="(msg, index) in history" :key="index">
                <div class="flex w-full" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <div class="flex max-w-[85%] gap-3" :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'">
                        
                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center border border-gray-200 dark:border-gray-600"
                             :class="msg.role === 'user' ? 'bg-primary-600' : 'bg-white dark:bg-gray-800'">
                            <span x-show="msg.role === 'user'" class="text-xs text-white font-bold">U</span>
                            <x-heroicon-m-cpu-chip x-show="msg.role !== 'user'" class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                        </div>

                        <div class="p-3.5 rounded-2xl shadow-sm text-sm leading-relaxed overflow-hidden"
                             :class="msg.role === 'user' 
                                ? 'bg-primary-600 text-white rounded-tr-none' 
                                : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200 rounded-tl-none'">
                             
                             {{-- 
                                2. RENDER MARKDOWN HERE 
                                - If User: Show plain text (safer)
                                - If AI: Parse Markdown to HTML and use 'prose' classes for styling lists/bold
                             --}}
                             <div 
                                x-html="msg.role === 'user' ? msg.parts[0].text : parseMarkdown(msg.parts[0].text)"
                                class="prose dark:prose-invert prose-sm max-w-none 
                                       prose-p:my-1 prose-ul:my-1 prose-li:my-0 prose-headings:my-2"
                             ></div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="loading" class="flex w-full justify-start animate-pulse">
                <div class="flex max-w-[85%] gap-3 flex-row">
                    <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
                        <x-heroicon-m-cpu-chip class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                    </div>
                    <div class="p-4 rounded-2xl rounded-tl-none bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex space-x-1 items-center h-10">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-75"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-150"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 border-x border-b border-gray-200 dark:border-gray-700 rounded-b-xl p-4 z-10">
            <div class="relative flex items-end gap-2">
                <div class="flex-grow" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()">
                    {{ $this->form }}
                </div>
                
                <button
                    type="button"
                    @click="sendMessage()"
                    :disabled="loading"
                    class="mb-1 inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 disabled:opacity-70 disabled:pointer-events-none"
                >
                    <span x-show="!loading" class="flex items-center gap-1">
                        Envoyer <x-heroicon-m-paper-airplane class="w-4 h-4" />
                    </span>
                    <span x-show="loading">...</span>
                </button>
            </div>
            <div class="text-center text-[10px] text-gray-400 mt-2">
                Fido peut faire des erreurs. Vérifiez les données importantes.
            </div>
        </div>
    </div>

    <script>
        function aiChat() {
            return {
                history: @js($this->chatHistory),
                loading: false,
                
                initChat() {
                    if (this.history.length === 0) {
                        this.history.push({
                            role: 'model',
                            parts: [{ text: "Bonjour {{ auth()->user()->name }}, je suis Fido. Je vois vos données, posez-moi une question !" }]
                        });
                    }
                },

                // 3. HELPER FUNCTION TO PARSE MARKDOWN
                parseMarkdown(text) {
                    if (!text) return '';
                    // 'marked.parse' comes from the script included at the top
                    return marked.parse(text);
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const box = this.$refs.chatContainer;
                        if(box) box.scrollTop = box.scrollHeight;
                    });
                },

                async sendMessage() {
                    const input = document.querySelector('textarea'); 
                    if (!input) return;

                    const text = input.value;
                    if (!text.trim() || this.loading) return;

                    this.history.push({ role: 'user', parts: [{ text: text }] });
                    input.value = '';
                    input.dispatchEvent(new Event('input')); 
                    this.loading = true;
                    this.scrollToBottom();

                    try {
                        const response = await fetch('{{ route("gemini.stream") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                prompt: text,
                                history: this.history.slice(0, -1),
                                tenant_id: '{{ \Filament\Facades\Filament::getTenant()?->id ?? auth()->user()->currentTenant?->id }}'
                            })
                        });

                        if (!response.ok) throw new Error('Erreur API');

                        this.history.push({ role: 'model', parts: [{ text: '' }] });
                        const msgIndex = this.history.length - 1;
                        this.scrollToBottom();

                        const reader = response.body.getReader();
                        const decoder = new TextDecoder();

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            const chunk = decoder.decode(value, { stream: true });
                            const lines = chunk.split('\n');
                            
                            lines.forEach(line => {
                                if (line.startsWith('data: ')) {
                                    try {
                                        const data = JSON.parse(line.substring(6));
                                        if (data.text) {
                                            this.history[msgIndex].parts[0].text += data.text;
                                            this.scrollToBottom();
                                        }
                                    } catch (e) {}
                                }
                            });
                        }
                        
                        @this.saveConversation(this.history);

                    } catch (error) {
                        console.error(error);
                        if (this.history[this.history.length - 1].role !== 'model') {
                            this.history.push({ role: 'model', parts: [{ text: "Erreur de connexion." }] });
                        }
                    } finally {
                        this.loading = false;
                        this.scrollToBottom();
                    }
                }
            }
        }
    </script>
</x-filament-panels::page>