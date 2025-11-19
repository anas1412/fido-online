<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fido Logs Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        /* Custom Sushi Green Theme */
        .bg-sushi { background-color: #6fbf44; }
        .text-sushi { color: #6fbf44; }
        .border-sushi { border-color: #6fbf44; }
        .hover-bg-sushi:hover { background-color: #61a83a; }
        .hover-text-sushi:hover { color: #61a83a; }
        .ring-sushi:focus { --tw-ring-color: #6fbf44; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cfcfcf; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6fbf44; }

        [x-cloak] { display: none !important; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sushi: { 50: '#f4fbf2', 100: '#e3f6de', 500: '#6fbf44', 600: '#5da537' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-slate-800 antialiased" x-data="logManager()">

    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0">
        <div class="px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Logo -->
                <div class="w-8 h-8 rounded-lg bg-sushi flex items-center justify-center text-white font-bold shadow-lg shadow-sushi/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-gray-800">Fido<span class="text-sushi">Logs</span></span>
                
                <!-- File Indicator -->
                <div class="hidden md:block ml-6 px-3 py-1 bg-gray-100 rounded text-xs font-mono text-gray-500" x-show="fileName" x-cloak>
                    Reading: <span class="font-semibold text-gray-700" x-text="fileName"></span>
                </div>
            </div>
            
            <!-- Live Toggle Button -->
            <div class="flex items-center gap-4">
                <button 
                    @click="toggleLive()" 
                    class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold transition-all border"
                    :class="live ? 'bg-sushi-50 text-sushi border-sushi' : 'bg-gray-100 text-gray-500 border-gray-200'"
                >
                    <span class="w-2 h-2 rounded-full" :class="live ? 'bg-sushi animate-pulse' : 'bg-gray-400'"></span>
                    <span x-text="live ? 'LIVE UPDATE ON' : 'LIVE UPDATE OFF'"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Events -->
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Total Events</p>
                    <h3 class="text-2xl font-bold text-gray-800" x-text="stats.total">0</h3>
                </div>
                <div class="p-3 bg-sushi-50 text-sushi rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>
            <!-- Errors -->
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Errors</p>
                    <h3 class="text-2xl font-bold text-red-500" x-text="stats.errors">0</h3>
                </div>
                <div class="p-3 bg-red-50 text-red-500 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <!-- Warnings -->
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase">Warnings</p>
                    <h3 class="text-2xl font-bold text-orange-500" x-text="stats.warnings">0</h3>
                </div>
                <div class="p-3 bg-orange-50 text-orange-500 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input 
                    type="text" 
                    x-model.debounce.500ms="search" 
                    placeholder="Search logs by message or date..." 
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-sushi focus:border-sushi outline-none"
                >
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <select x-model="level" class="px-4 py-2.5 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-sushi focus:border-sushi outline-none cursor-pointer">
                    <option value="ALL">All Levels</option>
                    <option value="ERROR">Errors Only</option>
                    <option value="WARNING">Warnings Only</option>
                    <option value="INFO">Info Only</option>
                    <option value="DEBUG">Debug Only</option>
                </select>
                <button @click="fetchLogs()" class="p-2.5 text-gray-500 hover:text-sushi hover:bg-sushi-50 rounded-lg transition-colors" title="Refresh">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div x-show="isLoading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-sushi"></div>
        </div>

        <!-- Logs Table -->
        <div x-show="!isLoading" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-cloak>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Level</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(log, index) in logs" :key="index">
                            <tr class="hover:bg-gray-50 group transition-colors">
                                <!-- Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold border" :class="log.color" x-text="log.level"></span>
                                </td>
                                <!-- Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono" x-text="log.date"></td>
                                <!-- Message -->
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="max-w-2xl truncate font-mono text-xs" x-text="log.summary"></div>
                                </td>
                                <!-- Button -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button @click="openModal(log)" class="text-sushi font-medium hover:text-sushi-600 flex items-center justify-end gap-1 w-full">
                                        View
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <!-- Empty State -->
                        <tr x-show="logs.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-lg font-medium text-gray-500">No logs found</p>
                                    <p class="text-sm mt-1">Try adjusting your search filters or check if the log file is empty.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between" x-show="logs.length > 0">
                <span class="text-sm text-gray-500">
                    Page <span class="font-bold text-gray-800" x-text="pagination.current_page"></span> of <span class="font-bold text-gray-800" x-text="pagination.last_page"></span>
                </span>
                <div class="flex gap-2">
                    <button 
                        @click="prevPage" 
                        :disabled="pagination.current_page <= 1"
                        class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >Previous</button>
                    <button 
                        @click="nextPage" 
                        :disabled="pagination.current_page >= pagination.last_page"
                        class="px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >Next</button>
                </div>
            </div>
        </div>

    </main>

    <!-- Details Modal -->
    <div x-show="selectedLog" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div 
                x-show="selectedLog" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="selectedLog = null" 
                class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Panel -->
            <div 
                x-show="selectedLog"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
            >
                <div class="bg-white px-6 pt-5 pb-4 sm:p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Log Details</h3>
                            <p class="text-sm text-gray-500 mt-1">Full stack trace and environment info.</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border" :class="selectedLog?.color" x-text="selectedLog?.level"></span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                             <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <span class="block text-xs text-gray-400 uppercase font-semibold">Timestamp</span>
                                <span class="text-sm font-mono text-gray-800" x-text="selectedLog?.date"></span>
                            </div>
                             <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <span class="block text-xs text-gray-400 uppercase font-semibold">Environment</span>
                                <span class="text-sm font-mono text-gray-800" x-text="selectedLog?.env || 'Production'"></span>
                            </div>
                        </div>

                        <div>
                            <div class="bg-gray-900 rounded-lg p-4 overflow-auto max-h-96 shadow-inner">
                                <pre class="text-xs text-green-400 font-mono whitespace-pre-wrap leading-relaxed" x-text="selectedLog?.message"></pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button @click="selectedLog = null" class="inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-2 bg-sushi text-base font-medium text-white hover:bg-sushi-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sushi sm:text-sm transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function logManager() {
            return {
                logs: [],
                stats: { total: 0, errors: 0, warnings: 0 },
                pagination: { current_page: 1, last_page: 1 },
                search: '',
                level: 'ALL',
                fileName: '',
                isLoading: false,
                selectedLog: null,
                live: false,
                interval: null,

                init() {
                    this.fetchLogs();
                    // Re-fetch when filters change
                    this.$watch('search', () => { this.pagination.current_page = 1; this.fetchLogs(); });
                    this.$watch('level', () => { this.pagination.current_page = 1; this.fetchLogs(); });
                },

                toggleLive() {
                    this.live = !this.live;
                    if (this.live) {
                        this.fetchLogs(true); // Immediate silent update
                        this.interval = setInterval(() => {
                            // Only refresh if on page 1 and no search active (to prevent jumping while reading)
                            if(this.pagination.current_page === 1 && !this.search) {
                                this.fetchLogs(true);
                            }
                        }, 3000);
                    } else {
                        clearInterval(this.interval);
                    }
                },

                fetchLogs(silent = false) {
                    if (!silent) this.isLoading = true;

                    // Force JSON response with query param
                    const url = `/logs?json=true&page=${this.pagination.current_page}&search=${this.search}&level=${this.level}`;

                    fetch(url, {
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.logs = data.data;
                        this.pagination.current_page = data.current_page;
                        this.pagination.last_page = data.last_page;
                        this.stats = data.stats;
                        this.fileName = data.file_name;
                        this.isLoading = false;
                    })
                    .catch(err => {
                        console.error('Error fetching logs:', err);
                        this.isLoading = false;
                    });
                },

                nextPage() {
                    if (this.pagination.current_page < this.pagination.last_page) {
                        this.pagination.current_page++;
                        this.fetchLogs();
                    }
                },

                prevPage() {
                    if (this.pagination.current_page > 1) {
                        this.pagination.current_page--;
                        this.fetchLogs();
                    }
                },

                openModal(log) {
                    this.selectedLog = log;
                }
            }
        }
    </script>
</body>
</html>