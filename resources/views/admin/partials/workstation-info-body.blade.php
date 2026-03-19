<!-- OUTER WRAPPER WITH HORIZONTAL BANNER AT THE TOP -->
<div class="flex flex-col h-full bg-transparent w-full">

    <!-- HORIZONTAL BANNER -->
    <div class="w-full shrink-0 relative overflow-hidden"
         :class="theme === 'perfectlum' ? 'bg-gradient-to-r from-blue-600 to-sky-500' : 'bg-gradient-to-r from-indigo-900 to-slate-800'">
         <!-- Pattern overlay -->
         <div class="absolute inset-x-0 inset-y-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 16px 16px;"></div>
         <!-- Glow -->
         <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 w-48 h-48 bg-white/20 blur-[50px] rounded-full text-white"></div>
         
         <!-- SVG Abstract Illustration -->
         <svg class="absolute right-0 top-1/2 -translate-y-1/2 h-[200%] w-auto opacity-30 pointer-events-none" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="100" cy="100" r="80" stroke="url(#w_paint0)" stroke-width="12"/>
            <circle cx="100" cy="100" r="50" stroke="url(#w_paint1)" stroke-width="4"/>
            <path d="M20 100L180 100" stroke="url(#w_paint2)" stroke-width="2" stroke-linecap="round" stroke-dasharray="10 10"/>
            <defs>
                <linearGradient id="w_paint0" x1="20" y1="20" x2="180" y2="180">
                    <stop stop-color="white" stop-opacity="0.8"/>
                    <stop offset="1" stop-color="white" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="w_paint1" x1="50" y1="50" x2="150" y2="150">
                    <stop stop-color="white" stop-opacity="0.4"/>
                    <stop offset="1" stop-color="white" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="w_paint2" x1="20" y1="100" x2="180" y2="100">
                    <stop stop-color="white" stop-opacity="0.1"/>
                    <stop offset="0.5" stop-color="white" stop-opacity="0.6"/>
                    <stop offset="1" stop-color="white" stop-opacity="0.1"/>
                </linearGradient>
            </defs>
         </svg>

        <div class="px-6 py-5 lg:py-6 relative z-10 flex items-center justify-start gap-4 lg:gap-5">
            <!-- Icon Block -->
            <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-2xl flex items-center justify-center border bg-white/10 backdrop-blur-md shadow-xl shrink-0"
                 :class="theme === 'perfectlum' ? 'border-white/40' : 'border-white/20'">
                <i data-lucide="monitor-speaker" class="w-6 h-6 lg:w-7 lg:h-7 text-white"></i>
            </div>
            
            <!-- Titles -->
            <div class="flex-1 text-left">
                <p class="text-white/80 text-[10px] lg:text-[12px] font-bold tracking-wider uppercase mb-1 drop-shadow-sm">Workstation Details</p>
                <h2 class="text-xl lg:text-3xl font-bold tracking-tight text-white drop-shadow-md" x-text="selectedWorkstation ? selectedWorkstation.name : 'Unknown Workstation'"></h2>
            </div>
        </div>
    </div>

    <!-- MAIN BODY -->
    <div class="flex-1 flex flex-col lg:flex-row p-0 overflow-hidden relative">

        <!-- LEFT PANEL -->
        <div class="lg:w-[320px] shrink-0 flex flex-col h-full border-r overflow-y-auto p-0"
             :class="theme === 'perfectlum' ? 'bg-white border-gray-100' : 'bg-[#111216] border-white/5'">
            
            <div class="p-8">
                <div class="flex flex-col gap-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-[11px] font-medium opacity-60 mb-1">Workstation Name</p>
                            <p class="text-[14px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'" x-text="selectedWorkstation ? selectedWorkstation.name : 'DESKTOP-FEB6J0R'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[11px] font-medium opacity-60 mb-1">Workgroup</p>
                                <button @click="openWorkgroupInfo({name: selectedWorkstation && selectedWorkstation.workgroup ? selectedWorkstation.workgroup : 'Qs-group test'})" class="text-[14px] font-semibold text-sky-500 hover:text-sky-600 hover:underline transition-colors text-left" x-text="selectedWorkstation && selectedWorkstation.workgroup ? selectedWorkstation.workgroup : 'Qs-group test'"></button>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium opacity-60 mb-1 capitalize">Facility</p>
                                <button @click="openFacilityInfo({name: selectedWorkstation && selectedWorkstation.facility ? selectedWorkstation.facility : 'Felenko', location: 'Main location', description: 'Main scanning area.'})" class="text-[14px] font-semibold text-sky-500 hover:text-sky-600 hover:underline transition-colors text-left" x-text="selectedWorkstation && selectedWorkstation.facility ? selectedWorkstation.facility : 'Felenko'"></button>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium opacity-60 mb-1">Department</p>
                                <p class="text-[14px] font-semibold opacity-50" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">-</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium opacity-60 mb-1">Room Number</p>
                                <p class="text-[14px] font-semibold opacity-50" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="border-dashed" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">

                    <!-- Tech Info -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-[11px] font-medium opacity-60 mb-1">Last Connected</p>
                            <p class="text-[13px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">2019-08-28 04:59:54</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium opacity-60 mb-1">License Code</p>
                            <p class="text-[13px] font-mono break-all" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">1R6KTM7GLIAF</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[11px] font-medium opacity-60 mb-1">Calibrate License</p>
                                <p class="text-[14px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Yes</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium opacity-60 mb-1">Qa License</p>
                                <p class="text-[14px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">Yes</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] font-medium opacity-60 mb-1">Client Version</p>
                            <p class="text-[14px] font-semibold" :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-gray-200'">PerfectLum Suite 4</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Attached Displays -->
        <div class="flex-1 flex flex-col h-full bg-transparent p-8">
            
            <h3 class="text-[16px] font-bold tracking-tight mb-6">Attached Displays</h3>

            <!-- Displays Table -->
            <div class="flex-1 rounded-xl border overflow-y-auto overflow-x-hidden relative" :class="theme === 'perfectlum' ? 'bg-white border-gray-200' : 'bg-[#111216] border-white/10'">
                <table class="w-full text-left text-[13px] whitespace-nowrap lg:min-w-[700px]">
                    <thead class="sticky top-0 z-20 backdrop-blur-md" :class="theme === 'perfectlum' ? 'bg-gray-50/90 border-b border-gray-200 text-gray-500' : 'bg-[#1a1c23]/90 border-b border-white/10 text-gray-400'">
                        <tr>
                            <th class="px-5 py-3 font-medium">Display Name</th>
                            <th class="px-5 py-3 font-medium">Model</th>
                            <th class="px-5 py-3 font-medium">Serial</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                        <tbody class="divide-y" :class="theme === 'perfectlum' ? 'divide-gray-100' : 'divide-white/5'">
                            <!-- Display 1 -->
                            <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                                <td class="px-5 py-4 font-semibold text-[#0ea5e9] cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (9N6WXV2)'})">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="monitor" class="w-4 h-4 opacity-50"></i>
                                        Dell U3219Q
                                    </div>
                                </td>
                                <td class="px-5 py-4 opacity-80 cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (9N6WXV2)'})">DELL U3219Q</td>
                                <td class="px-5 py-4 opacity-80 cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (9N6WXV2)'})">9N6WXV2</td>
                                <td class="px-5 py-4 cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (9N6WXV2)'})">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                                        <i data-lucide="check" class="w-3 h-3"></i> Passed
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click.stop="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all" :class="theme === 'perfectlum' ? 'bg-white border-gray-200 hover:bg-gray-50' : 'bg-[#1a1c23] border-white/10 hover:bg-white/5'">
                                            <i data-lucide="more-horizontal" class="w-4 h-4 opacity-60"></i>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" class="absolute right-0 top-10 w-44 rounded-xl shadow-2xl border z-[30] overflow-hidden" :class="theme === 'perfectlum' ? 'bg-white border-gray-200' : 'bg-[#1a1c23] border-white/10'" style="display: none;">
                                            <button @click="open = false; openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (9N6WXV2)'})" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold hover:bg-[#0ea5e9] hover:text-white transition-colors">
                                                <i data-lucide="settings" class="w-3.5 h-3.5"></i> Display Setting
                                            </button>
                                            <button @click="open = false; workstationViewState = 'delete-display'; deleteDisplayTarget = 'Dell U3219Q (9N6WXV2)'" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold text-rose-500 hover:bg-rose-500 hover:text-white transition-colors">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete Display
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Display 2 -->
                            <tr class="transition-colors hover:bg-black/5 dark:hover:bg-white/5">
                                <td class="px-5 py-4 font-semibold text-[#0ea5e9] cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (D96WXV2)'})">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="monitor" class="w-4 h-4 opacity-50"></i>
                                        Dell U3219Q
                                    </div>
                                </td>
                                <td class="px-5 py-4 opacity-80 cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (D96WXV2)'})">DELL U3219Q</td>
                                <td class="px-5 py-4 opacity-80 cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (D96WXV2)'})">D96WXV2</td>
                                <td class="px-5 py-4 cursor-pointer" @click="openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (D96WXV2)'})">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                                        <i data-lucide="check" class="w-3 h-3"></i> Passed
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click.stop="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all" :class="theme === 'perfectlum' ? 'bg-white border-gray-200 hover:bg-gray-50' : 'bg-[#1a1c23] border-white/10 hover:bg-white/5'">
                                            <i data-lucide="more-horizontal" class="w-4 h-4 opacity-60"></i>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" class="absolute right-0 top-10 w-44 rounded-xl shadow-2xl border z-[30] overflow-hidden" :class="theme === 'perfectlum' ? 'bg-white border-gray-200' : 'bg-[#1a1c23] border-white/10'" style="display: none;">
                                            <button @click="open = false; openDisplaySettingsFromWorkstation({name: 'Dell U3219Q (D96WXV2)'})" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold hover:bg-[#0ea5e9] hover:text-white transition-colors">
                                                <i data-lucide="settings" class="w-3.5 h-3.5"></i> Display Setting
                                            </button>
                                            <button @click="open = false; workstationViewState = 'delete-display'; deleteDisplayTarget = 'Dell U3219Q (D96WXV2)'" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold text-rose-500 hover:bg-rose-500 hover:text-white transition-colors">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete Display
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>

            <p class="mt-4 text-[12px] opacity-50 italic">Tip: Click on any display row above to view and modify its specific settings.</p>

        </div>
        
        <!-- INLINE VIEW: DELETE DISPLAY -->
        <div x-show="workstationViewState === 'delete-display'" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-x-8" 
             x-transition:enter-end="opacity-100 translate-x-0"
             class="absolute inset-0 z-20 flex"
             :class="theme === 'perfectlum' ? 'bg-white' : 'bg-[#0b0c10]'"
             style="display: none;">
            
             <div class="w-full lg:w-1/2 p-8 lg:p-12 border-l" :class="theme === 'perfectlum' ? 'border-gray-100 bg-gray-50/50' : 'border-white/5'">
                <button @click="workstationViewState = 'info'" class="group flex items-center gap-2 text-rose-500 font-bold text-[13px] mb-8 hover:translate-x-[-4px] transition-transform">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
                </button>
                
                <div class="bg-white dark:bg-[#111216] rounded-2xl border p-8 shadow-xl" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
                    <div class="flex items-center gap-5 mb-10 border-b pb-8" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/10'">
                        <div class="w-14 h-14 rounded-2xl bg-rose-500 flex items-center justify-center text-white shadow-lg shadow-rose-500/20">
                            <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-rose-500">Delete Display?</h2>
                            <p class="text-[13px] opacity-60">This action cannot be undone.</p>
                        </div>
                    </div>
                    
                    <div class="space-y-6 text-center">
                        <div class="p-6 rounded-xl" :class="theme === 'perfectlum' ? 'bg-rose-50' : 'bg-rose-500/10'">
                            <p class="text-[14px] mb-2 font-medium" :class="theme === 'perfectlum' ? 'text-rose-900' : 'text-rose-200'">You are about to permanently delete:</p>
                            <p class="text-[18px] font-bold text-rose-500" x-text="deleteDisplayTarget"></p>
                        </div>
                        
                        <p class="text-[13px] opacity-70">All calibration histories and QA records associated with this display will be permanently removed from this workstation.</p>
                        
                        <div class="pt-6 flex gap-3 justify-center">
                            <button @click="workstationViewState = 'info'; /* Add delete logic */" class="px-8 h-12 rounded-xl bg-rose-500 text-white font-bold shadow-lg shadow-rose-500/20 transition-transform active:scale-95">Yes, Delete Display</button>
                            <button @click="workstationViewState = 'info'" class="px-8 h-12 rounded-xl border dark:border-white/10 font-bold transition-all hover:bg-black/5 dark:hover:bg-white/5">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
