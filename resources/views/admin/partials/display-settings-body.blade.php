<!-- OUTER WRAPPER WITH HORIZONTAL BANNER AT THE TOP -->
<div class="flex flex-col h-full bg-transparent w-full">

    <!-- HORIZONTAL BANNER -->
    <div class="w-full shrink-0 relative overflow-hidden"
         :class="theme === 'perfectlum' ? 'bg-gradient-to-r from-blue-600 to-sky-500' : 'bg-gradient-to-r from-indigo-900 to-blue-900'">
         
        <!-- Pattern & Glow -->
        <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
        <div class="absolute -right-20 top-0 bottom-0 w-64 bg-white/10 blur-3xl transform skew-x-12"></div>
        <div class="absolute -left-20 top-0 bottom-0 w-64 bg-black/20 blur-3xl transform skew-x-12"></div>

        <!-- SVG Abstract Illustration -->
        <svg class="absolute right-0 lg:right-20 top-0 h-[150%] w-auto opacity-30 pointer-events-none transform -translate-y-1/4" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M50 150 C 150 50, 250 250, 350 150" stroke="url(#d_paint0)" stroke-width="12" stroke-linecap="round"/>
            <path d="M100 150 C 200 100, 200 200, 300 150" stroke="url(#d_paint1)" stroke-width="6" stroke-linecap="round"/>
            <circle cx="200" cy="150" r="100" stroke="url(#d_paint2)" stroke-width="20" stroke-dasharray="2 12" stroke-linecap="round"/>
            <defs>
                <linearGradient id="d_paint0" x1="50" y1="150" x2="350" y2="150">
                    <stop stop-color="white" stop-opacity="0.9"/>
                    <stop offset="1" stop-color="white" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="d_paint1" x1="100" y1="150" x2="300" y2="150">
                    <stop stop-color="white" stop-opacity="0.5"/>
                    <stop offset="1" stop-color="white" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="d_paint2" x1="100" y1="50" x2="300" y2="250">
                    <stop stop-color="white" stop-opacity="0.8"/>
                    <stop offset="1" stop-color="white" stop-opacity="0"/>
                </linearGradient>
            </defs>
        </svg>

        <div class="px-6 py-5 lg:py-6 relative z-10 flex items-center justify-start gap-4 lg:gap-5">
            <!-- Icon Block -->
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl flex items-center justify-center border bg-white/10 backdrop-blur-md shadow-xl shrink-0"
                 :class="theme === 'perfectlum' ? 'border-white/40' : 'border-white/20'">
                <i data-lucide="settings" class="w-6 h-6 lg:w-7 lg:h-7 text-white animate-[spin_4s_linear_infinite]"></i>
            </div>
            
            <!-- Titles -->
            <div class="flex-1 text-center lg:text-left">
                <p class="text-white/80 text-[10px] lg:text-[12px] font-bold tracking-wider uppercase mb-1 drop-shadow-sm">Display Settings Configuration</p>
                <h2 class="text-xl lg:text-2xl xl:text-3xl font-bold tracking-tight text-white drop-shadow-md">
                    <span x-text="selectedDisplay ? selectedDisplay.name : 'Unknown Display'"></span>
                </h2>
            </div>
        </div>
    </div>

    <!-- MAIN BODY -->
    <div class="flex-1 flex flex-col lg:flex-row p-0 overflow-hidden relative">
            
            <!-- LEFT PANEL: Sidebar/Summary -->
            <div class="lg:w-[320px] shrink-0 flex flex-col h-full border-r overflow-y-auto"
                 :class="theme === 'perfectlum' ? 'bg-white border-gray-100' : 'bg-[#111216] border-white/5'">
                
                <div class="p-6">
                    <h2 class="text-[14px] font-bold tracking-wide mb-4" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Display Hierarchy</h2>
                    
                    <div class="space-y-4">
                        <!-- Facility -->
                        <div>
                            <label class="block text-[11px] font-medium opacity-60 mb-1 pl-1">Facility:</label>
                            <div class="w-full h-9 px-3 flex items-center rounded-lg text-[12px] font-medium transition-colors"
                                 :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-700' : 'bg-white/5 text-white'">
                                NYU Langone
                            </div>
                        </div>

                        <!-- Workgroup -->
                        <div>
                            <label class="block text-[11px] font-medium opacity-60 mb-1 pl-1">Workgroup:</label>
                            <div class="w-full h-9 px-3 flex items-center rounded-lg text-[12px] font-medium transition-colors"
                                 :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-700' : 'bg-white/5 text-white'">
                                NYULH HOME
                            </div>
                        </div>

                        <!-- Workstation -->
                        <div>
                            <label class="block text-[11px] font-medium opacity-60 mb-1 pl-1">Workstation:</label>
                            <div class="w-full h-9 px-3 flex items-center rounded-lg text-[12px] font-medium transition-colors"
                                 :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-700' : 'bg-white/5 text-white'">
                                PACSHMSDOH0501
                            </div>
                        </div>

                        <!-- Display -->
                        <div>
                            <label class="block text-[11px] font-medium opacity-60 mb-1 pl-1">Display:</label>
                            <div class="w-full h-9 px-3 flex items-center rounded-lg text-[12px] font-bold border transition-colors"
                                 :class="theme === 'perfectlum' ? 'bg-green-50 text-green-700 border-green-500/30' : 'bg-green-500/10 text-green-400 border-green-500/30'">
                                <span x-text="selectedDisplay ? selectedDisplay.name : 'Dell U3219Q (9N6WXV2)'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/10'">
                        <div class="p-4 rounded-xl" :class="theme === 'perfectlum' ? 'bg-gray-50' : 'bg-[#1A1C23]'">
                            <p class="text-[11px] font-medium opacity-60 mb-1">Status</p>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                <span class="text-[16px] font-bold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-100'">All Tests Passed</span>
                            </div>
                            <div class="mt-3 flex gap-2">
                                <button class="flex-1 h-8 rounded-lg text-[12px] font-bold bg-[#0ea5e9] text-white hover:bg-sky-600 transition-colors shadow-sm focus:ring-2 focus:ring-sky-500/50">Run Test</button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-lg border transition-colors hover:bg-black/5 dark:hover:bg-white/5" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
                                    <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4">
                        <h3 class="text-[12px] font-bold opacity-50 uppercase tracking-wider">Device Details</h3>
                        
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5" :class="theme === 'perfectlum' ? 'bg-gray-100 text-gray-500' : 'bg-white/5 text-gray-400'">
                                    <i data-lucide="monitor" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-medium opacity-60">Model</p>
                                    <p class="text-[13px] font-medium" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">DELL U3219Q</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5" :class="theme === 'perfectlum' ? 'bg-gray-100 text-gray-500' : 'bg-white/5 text-gray-400'">
                                    <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-medium opacity-60">Serial Number</p>
                                    <p class="text-[13px] font-medium" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">9N6WXV2</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5" :class="theme === 'perfectlum' ? 'bg-gray-100 text-gray-500' : 'bg-white/5 text-gray-400'">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-medium opacity-60">Installation Date</p>
                                    <p class="text-[13px] font-medium" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Dec 16, 2021</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: Breadcrumbs, Tabs, Content -->
            <div class="flex-1 flex flex-col h-full bg-transparent">
                
                <!-- Right Header: Breadcrumbs -->
                <div class="px-8 pt-6 pb-6 shrink-0">
                    <div class="flex items-center gap-2 text-[13px] font-medium" :class="theme === 'perfectlum' ? 'text-gray-500' : 'text-gray-400'">
                        <span>Hierarchy:</span>
                        <span class="font-bold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">NYU Langone</span>
                        <i data-lucide="chevron-right" class="w-3 h-3 opacity-50"></i>
                        <span class="font-bold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">PACSHMSDOH0501</span>
                        <i data-lucide="chevron-right" class="w-3 h-3 opacity-50"></i>
                        <span class="font-bold text-[#0ea5e9]" x-text="selectedDisplay ? selectedDisplay.name : ''"></span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="px-8 shrink-0">
                    <div class="flex gap-6 border-b" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
                        <button @click="displaySettingsTab = 'settings'" 
                            class="pb-3 text-[13px] font-bold transition-colors relative"
                            :class="displaySettingsTab === 'settings' 
                                ? 'text-[#0ea5e9]' 
                                : (theme === 'perfectlum' ? 'text-gray-500 hover:text-gray-800' : 'text-gray-400 hover:text-gray-200')">
                            Settings
                            <span x-show="displaySettingsTab === 'settings'" class="absolute -bottom-px left-0 right-0 h-0.5 bg-[#0ea5e9] rounded-t-full"></span>
                        </button>
                        <button @click="displaySettingsTab = 'financial'" 
                            class="pb-3 text-[13px] font-bold transition-colors relative"
                            :class="displaySettingsTab === 'financial' 
                                ? 'text-[#0ea5e9]' 
                                : (theme === 'perfectlum' ? 'text-gray-500 hover:text-gray-800' : 'text-gray-400 hover:text-gray-200')">
                            Financial Status
                            <span x-show="displaySettingsTab === 'financial'" class="absolute -bottom-px left-0 right-0 h-0.5 bg-[#0ea5e9] rounded-t-full"></span>
                        </button>
                    </div>
                </div>

                <!-- SCROLLABLE CONTENT AREA -->
                <div class="flex-1 overflow-y-auto p-8 no-scrollbar">
                    
                    <!-- ================= SETTINGS TAB ================= -->
                    <div x-show="displaySettingsTab === 'settings'" class="max-w-3xl space-y-8 animate-fade-in" style="display: none;">
                        
                        <!-- Checkboxes / Toggles section (Compact) -->
                        <div class="bg-white dark:bg-[#111216] rounded-xl border p-5 space-y-4" :class="theme === 'perfectlum' ? 'border-gray-200 shadow-sm' : 'border-white/5'">
                            <h3 class="text-[12px] font-bold opacity-80 uppercase tracking-wider mb-2">Options</h3>
                            
                            <div class="flex items-center justify-between py-1">
                                <div>
                                    <p class="text-[13px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Ignore Display</p>
                                    <p class="text-[11px] opacity-60">Exclude Display from Testing / Calibration</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-sky-500"></div>
                                </label>
                            </div>

                            <hr class="border-t" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/5'">

                            <div class="flex items-center justify-between py-1">
                                <div>
                                    <p class="text-[13px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Calibration Upload</p>
                                    <p class="text-[11px] opacity-60">Use graphicboard LUTs only</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-sky-500"></div>
                                </label>
                            </div>
                            
                            <hr class="border-t" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/5'">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 py-1">
                                <label class="text-[13px] font-semibold w-full sm:w-1/3 shrink-0" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Save Calibration to</label>
                                <div class="relative flex-1">
                                    <select class="w-full h-8 pl-3 pr-8 rounded-lg text-[12px] font-medium outline-none border transition-colors appearance-none cursor-pointer"
                                            :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&>option]:bg-[#111216]'">
                                        <option value=""></option>
                                    </select>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 opacity-50 pointer-events-none"></i>
                                </div>
                            </div>

                            <hr class="border-t" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/5'">
                            
                            <div class="flex items-center justify-between py-1">
                                <div>
                                    <p class="text-[13px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Used Sensor</p>
                                    <p class="text-[11px] opacity-60">Use internal sensor if possible</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-sky-500"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Form Grid (Compact Layout) -->
                        <div class="bg-white dark:bg-[#111216] rounded-xl border p-5 space-y-4" :class="theme === 'perfectlum' ? 'border-gray-200 shadow-sm' : 'border-white/5'">
                            <h3 class="text-[12px] font-bold opacity-80 uppercase tracking-wider mb-2">Technical Specifications</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-x-6 gap-y-4">
                                <!-- Model -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Display Model</label>
                                    <input type="text" value="DELL U3219Q" 
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 w-full"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Serial -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Display Serial Number</label>
                                    <input type="text" value="9N6WXV2" 
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 w-full"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Manufacturer -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Manufacturer</label>
                                    <input type="text" value="Dell" 
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 w-full"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Inventory -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Inventory Number</label>
                                    <input type="text" placeholder="" 
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 w-full"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Type -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Type of Display</label>
                                    <div class="relative w-full">
                                        <select class="w-full h-8 pl-3 pr-8 rounded-lg text-[12px] font-medium outline-none border transition-colors appearance-none cursor-pointer"
                                                :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&>option]:bg-[#111216]'">
                                            <option>Color</option>
                                            <option>Grayscale</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 opacity-50 pointer-events-none"></i>
                                    </div>
                                </div>

                                <!-- Technology -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Technology</label>
                                    <div class="relative w-full">
                                        <select class="w-full h-8 pl-3 pr-8 rounded-lg text-[12px] font-medium outline-none border transition-colors appearance-none cursor-pointer"
                                                :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&>option]:bg-[#111216]'">
                                            <option>Flat/LCD CCFL backlight</option>
                                            <option selected>Flat/LCD LED backlight</option>
                                            <option>CRT</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 opacity-50 pointer-events-none"></i>
                                    </div>
                                </div>

                                <!-- Size -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Screen Size</label>
                                    <input type="text" value='31.5"' 
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 w-full"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Resolution -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Resolution (h/v)</label>
                                    <div class="flex items-center gap-2 w-full">
                                        <div class="relative flex-1 group">
                                            <input type="text" value="3840" 
                                                class="w-full h-8 pl-3 pr-6 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                                :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-bold opacity-40">px</span>
                                        </div>
                                        <span class="text-[10px] font-bold opacity-40">X</span>
                                        <div class="relative flex-1 group">
                                            <input type="text" value="2160" 
                                                class="w-full h-8 pl-3 pr-6 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                                :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-bold opacity-40">px</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Backlight Stabilization -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Backlight Stabilization</label>
                                    <div class="relative w-full">
                                        <select class="w-full h-8 pl-3 pr-8 rounded-lg text-[12px] font-medium outline-none border transition-colors appearance-none cursor-pointer"
                                                :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&>option]:bg-[#111216]'">
                                            <option>no</option>
                                            <option>yes</option>
                                        </select>
                                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 opacity-50 pointer-events-none"></i>
                                    </div>
                                </div>

                                <!-- Installation Date -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Installation Date</label>
                                    <input type="date" value="2021-12-16" 
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 w-full cursor-pointer"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&::-webkit-calendar-picker-indicator]:invert'">
                                </div>

                            </div>
                        </div>

                        <!-- Save Action -->
                        <div class="pt-2 flex justify-start gap-3">
                            <button class="px-6 h-9 rounded-full text-[12px] font-bold bg-[#0ea5e9] text-white hover:bg-sky-600 shadow-sm transition-all focus:ring-2 focus:ring-sky-500/50 cursor-pointer" @click="closeDisplaySettings()">
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- ================= FINANCIAL STATUS TAB ================= -->
                    <div x-show="displaySettingsTab === 'financial'" class="max-w-3xl space-y-6 animate-fade-in" style="display: none;">
                        
                        <div class="bg-white dark:bg-[#111216] rounded-xl border p-5 space-y-4" :class="theme === 'perfectlum' ? 'border-gray-200 shadow-sm' : 'border-white/5'">
                            <h3 class="text-[12px] font-bold opacity-80 uppercase tracking-wider mb-2">Financial Records</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                <!-- Date Of Purchase -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Date Of Purchase / Lease:</label>
                                    <input type="date"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 cursor-pointer"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&::-webkit-calendar-picker-indicator]:invert'">
                                </div>

                                <!-- Initial Value -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Initial Value:</label>
                                    <input type="text"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Expected value end -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Expected value at warranty end:</label>
                                    <input type="text"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Annual depreciation -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Annual straight line depreciation:</label>
                                    <input type="text"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Monthly depreciation -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Monthly straight line depreciation:</label>
                                    <input type="text"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Current value -->
                                <div class="flex flex-col">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Current value:</label>
                                    <input type="text"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white'">
                                </div>

                                <!-- Expected replacement date -->
                                <div class="flex flex-col md:col-span-2">
                                    <label class="text-[11px] font-semibold opacity-60 mb-1 pl-1">Expected replacement date:</label>
                                    <input type="date"
                                        class="h-8 px-3 rounded-lg text-[12px] font-medium outline-none border transition-colors focus:ring-2 cursor-pointer w-full md:w-1/2"
                                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 focus:border-sky-500 text-gray-700' : 'bg-[#1A1C23] border-white/10 focus:border-sky-400 text-white [&::-webkit-calendar-picker-indicator]:invert'">
                                </div>
                            </div>
                        </div>

                        <!-- Save Action -->
                        <div class="pt-2 flex justify-start gap-3">
                            <button class="px-6 h-9 rounded-full text-[12px] font-bold bg-[#0ea5e9] text-white hover:bg-sky-600 shadow-sm transition-all focus:ring-2 focus:ring-sky-500/50" @click="closeDisplaySettings()">
                                Save Changes
                            </button>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
