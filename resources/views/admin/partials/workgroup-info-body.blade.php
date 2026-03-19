<div class="flex-1 overflow-y-auto bg-transparent p-0 lg:p-8 no-scrollbar relative min-h-full">
    
    <!-- VIEW 1: SUMMARY & LIST (Default) -->
    <div x-show="workgroupViewState === 'workgroup'" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         class="space-y-6">
        
        <!-- HORIZONTAL BANNER (Kept for visual identity) -->
        <div class="w-full rounded-2xl overflow-hidden mb-6 relative shadow-lg"
             :class="theme === 'perfectlum' ? 'bg-gradient-to-r from-blue-600 to-cyan-500' : 'bg-gradient-to-r from-indigo-900 to-blue-900'">
             <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
             <div class="px-6 relative z-10 flex items-center justify-start gap-4 lg:gap-6 py-8 lg:py-10">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center border bg-white/10 backdrop-blur-md shadow-xl shrink-0"
                     :class="theme === 'perfectlum' ? 'border-white/40' : 'border-white/20'">
                    <i data-lucide="network" class="w-7 h-7 text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="text-white/80 text-[12px] font-bold tracking-wider uppercase mb-1 drop-shadow-sm">Workgroup Overview</p>
                    <h2 class="text-2xl lg:text-3xl font-bold tracking-tight text-white drop-shadow-md">
                        <span x-text="selectedWorkgroup ? selectedWorkgroup.name : 'Unknown'"></span>
                    </h2>
                </div>
             </div>
        </div>

        <!-- Details Card -->
        <div class="bg-white dark:bg-[#111216] rounded-xl border p-6" :class="theme === 'perfectlum' ? 'border-gray-200 shadow-sm' : 'border-white/10'">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-[14px] font-bold text-gray-800 dark:text-gray-200">Workgroup Details</h2>
                <button @click="workgroupViewState = 'edit-workgroup'" class="text-[12px] font-bold text-sky-500 hover:underline flex items-center gap-1">
                    <i data-lucide="pencil" class="w-3 h-3"></i> Quick Edit
                </button>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-[11px] font-bold opacity-60 mb-1">Facility</p>
                    <button @click="showWorkgroupInfoModal = false; setTimeout(() => { showFacilityInfoModal = true; selectedFacility = {name: 'Folenko', location: 'Radiology Room 1', description: 'Main scanning area.'} }, 300)" class="text-[13px] font-medium text-sky-500 hover:text-sky-600 hover:underline transition-colors text-left">Folenko</button>
                </div>
                <div>
                    <p class="text-[11px] font-bold opacity-60 mb-1">Address</p>
                    <p class="text-[13px] font-medium opacity-80">123 Radiology Blvd, NY 10001</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold opacity-60 mb-1">Phone Number</p>
                    <p class="text-[13px] font-medium opacity-80">+1 (555) 123-4567</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold opacity-60 mb-1">Last Sync</p>
                    <p class="text-[13px] font-medium opacity-80">Today, 10:45 AM</p>
                </div>
            </div>
        </div>

        <!-- Workstations Grid -->
        <div class="mt-8">
            <h2 class="text-[15px] font-bold text-gray-800 dark:text-gray-200 mb-5 flex items-center gap-2">
                <i data-lucide="monitor" class="w-4 h-4 opacity-50"></i>
                Workstations
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <template x-for="ws in [{name: 'DESKTOP-FEB6J0R', status: 'online', sleepTime: 'On'}, {name: 'DESKTOP-FEB6J1A', status: 'away', sleepTime: 'Off'}, {name: 'DESKTOP-FEB6J2B', status: 'offline', sleepTime: 'On'}]">
                    <div class="group relative bg-white dark:bg-[#111216] rounded-xl border p-5 transition-all hover:shadow-xl hover:-translate-y-1" 
                         :class="theme === 'perfectlum' ? 'border-gray-200 shadow-sm' : 'border-white/10 hover:border-white/20 shadow-lg'">
                         
                         <!-- Action Overlay (Modern Dropdown Replacement) -->
                         <div class="absolute top-3 right-3 z-20" x-data="{ open: false }">
                             <button @click.stop="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all" :class="theme === 'perfectlum' ? 'bg-white border-gray-200' : 'bg-[#1a1c23] border-white/10'">
                                 <i data-lucide="more-vertical" class="w-4 h-4 opacity-60"></i>
                             </button>
                             <div x-show="open" @click.outside="open = false" class="absolute right-0 top-9 w-44 rounded-xl shadow-2xl border z-[30] overflow-hidden" :class="theme === 'perfectlum' ? 'bg-white border-gray-200' : 'bg-[#1a1c23] border-white/10'">
                                 <button @click="open = false; workgroupViewState = 'edit-workstation'; editWorkstation = {name: ws.name}" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold hover:bg-sky-500 hover:text-white transition-colors">
                                     <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                 </button>
                                 <button @click="open = false; workgroupViewState = 'settings-workstation'; workstationSettingsName = ws.name" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold hover:bg-violet-600 hover:text-white transition-colors">
                                     <i data-lucide="settings" class="w-3.5 h-3.5"></i> Settings
                                 </button>
                                 <button @click="open = false; workgroupViewState = 'delete-workstation'; deleteWorkstationTarget = ws.name" class="w-full flex items-center gap-3 px-4 py-3 text-[12px] font-bold text-rose-500 hover:bg-rose-500 hover:text-white transition-colors">
                                     <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete
                                 </button>
                             </div>
                         </div>

                         <div @click="openWorkstationFromWorkgroup(ws)" class="cursor-pointer">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4 border shadow-sm transition-all group-hover:scale-110"
                                 :class="ws.status === 'online' ? 'bg-green-500 text-white shadow-green-500/20' : (ws.status === 'away' ? 'bg-amber-500 text-white shadow-amber-500/20' : 'bg-gray-400 text-white')">
                                <i data-lucide="monitor-speaker" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-bold text-[14px] mb-1 truncate" x-text="ws.name"></h3>
                            <div class="flex items-center justify-between mt-3 pt-3 border-t border-dashed opacity-60 transition-opacity" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" :class="ws.status === 'online' ? 'bg-green-500' : (ws.status === 'away' ? 'bg-amber-500' : 'bg-gray-500')"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest" x-text="ws.status"></span>
                                </div>
                                <div class="flex items-center gap-1" :class="ws.sleepTime === 'On' ? (theme === 'perfectlum' ? 'text-amber-600' : 'text-amber-400') : 'opacity-60'">
                                    <i data-lucide="moon" class="w-3 h-3"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-widest" x-text="ws.sleepTime"></span>
                                </div>
                            </div>
                         </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- VIEW 4: EDIT WORKGROUP (In-Pane) -->
    <div x-show="workgroupViewState === 'edit-workgroup'" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-x-8" 
         x-transition:enter-end="opacity-100 translate-x-0"
         class="max-w-xl mx-auto py-10">
        <button @click="backFromWorkgroupAction()" class="group flex items-center gap-2 text-sky-500 font-bold text-[13px] mb-8 hover:translate-x-[-4px] transition-transform">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> <span x-text="workgroupModalContext === 'workgroups' ? 'Cancel' : 'Back to Workgroup'"></span>
        </button>
        
        <div class="bg-white dark:bg-[#111216] rounded-2xl border p-8 shadow-xl" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
            <div class="flex items-center gap-5 mb-10">
                <div class="w-14 h-14 rounded-2xl bg-sky-500 flex items-center justify-center text-white shadow-lg shadow-sky-500/20">
                    <i data-lucide="pencil" class="w-7 h-7"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">Quick Edit Workgroup</h2>
                    <p class="text-[13px] opacity-60">Update basic information for this group</p>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest mb-2 opacity-50">Workgroup Name</label>
                        <input type="text" x-model="selectedWorkgroup.name" class="w-full h-12 px-4 rounded-xl border bg-transparent font-medium focus:ring-4 focus:ring-sky-500/10 transition-all outline-none" :class="theme === 'perfectlum' ? 'border-gray-200 bg-gray-50' : 'border-white/10 bg-white/5'">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest mb-2 opacity-50">Parent Facility</label>
                        <select x-model="selectedWorkgroup.facility" class="w-full h-12 px-4 rounded-xl border bg-transparent font-medium focus:ring-4 focus:ring-sky-500/10 transition-all outline-none appearance-none" :class="theme === 'perfectlum' ? 'border-gray-200 bg-gray-50' : 'border-white/10 bg-white/5'">
                            <option value="">Select Facility</option>
                            <option>Folenko</option>
                            <option>NYU Langone</option>
                            <option>Radiology Center</option>
                            <option>Main Hospital</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest mb-2 opacity-50">Address</label>
                        <input type="text" x-model="selectedWorkgroup.address" placeholder="Enter address" class="w-full h-12 px-4 rounded-xl border bg-transparent font-medium focus:ring-4 focus:ring-sky-500/10 transition-all outline-none" :class="theme === 'perfectlum' ? 'border-gray-200 bg-gray-50' : 'border-white/10 bg-white/5'">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest mb-2 opacity-50">Phone Number</label>
                        <input type="text" x-model="selectedWorkgroup.phone" placeholder="Enter phone number" class="w-full h-12 px-4 rounded-xl border bg-transparent font-medium focus:ring-4 focus:ring-sky-500/10 transition-all outline-none" :class="theme === 'perfectlum' ? 'border-gray-200 bg-gray-50' : 'border-white/10 bg-white/5'">
                    </div>
                </div>
                <div class="pt-6 flex gap-3">
                    <button @click="backFromWorkgroupAction()" class="px-8 h-12 rounded-xl bg-sky-500 text-white font-bold shadow-lg shadow-sky-500/20 transition-transform active:scale-95">Save Changes</button>
                    <button @click="backFromWorkgroupAction()" class="px-8 h-12 rounded-xl border dark:border-white/10 font-bold">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 6: DELETE WORKGROUP CONFIRMATION (In-Pane) -->
    <div x-show="workgroupViewState === 'delete-workgroup'" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-x-8" 
         x-transition:enter-end="opacity-100 translate-x-0"
         class="max-w-xl mx-auto py-10">
        <button @click="backFromWorkgroupAction()" class="group flex items-center gap-2 text-rose-500 font-bold text-[13px] mb-8 hover:translate-x-[-4px] transition-transform">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> <span x-text="workgroupModalContext === 'workgroups' ? 'Cancel' : 'Back to Workgroup'"></span>
        </button>
        
        <div class="bg-white dark:bg-[#111216] rounded-2xl border p-8 shadow-xl" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
            <div class="flex items-center gap-5 mb-10 border-b pb-8" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/10'">
                <div class="w-14 h-14 rounded-2xl bg-rose-500 flex items-center justify-center text-white shadow-lg shadow-rose-500/20">
                    <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-rose-500">Delete Workgroup?</h2>
                    <p class="text-[13px] opacity-60">This action cannot be undone.</p>
                </div>
            </div>
            
            <div class="space-y-6 text-center">
                <div class="p-6 rounded-xl" :class="theme === 'perfectlum' ? 'bg-rose-50' : 'bg-rose-500/10'">
                    <p class="text-[14px] mb-2 font-medium" :class="theme === 'perfectlum' ? 'text-rose-900' : 'text-rose-200'">You are about to permanently delete workgroup:</p>
                    <p class="text-[18px] font-bold text-rose-500" x-text="deleteWorkgroupTarget"></p>
                </div>
                
                <p class="text-[13px] opacity-70">All associated workstations, settings, and histories within this workgroup will be lost or reassigned based on system rules.</p>
                
                <div class="pt-6 flex gap-3 justify-center">
                    <button @click="backFromWorkgroupAction(); /* Add actual delete logic here */" class="px-8 h-12 rounded-xl bg-rose-500 text-white font-bold shadow-lg shadow-rose-500/20 transition-transform active:scale-95">Yes, Delete</button>
                    <button @click="backFromWorkgroupAction()" class="px-8 h-12 rounded-xl border dark:border-white/10 font-bold">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 2: EDIT WORKSTATION (In-Pane) -->
    <div x-show="workgroupViewState === 'edit-workstation'" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-x-8" 
         x-transition:enter-end="opacity-100 translate-x-0"
         class="max-w-xl mx-auto py-10">
        <button @click="backFromWorkstationAction()" class="group flex items-center gap-2 text-sky-500 font-bold text-[13px] mb-8 hover:translate-x-[-4px] transition-transform">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> <span x-text="workgroupModalContext === 'workstations' ? 'Cancel' : 'Back to Workgroup'"></span>
        </button>
        
        <div class="bg-white dark:bg-[#111216] rounded-2xl border p-8 shadow-xl" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
            <div class="flex items-center gap-5 mb-10">
                <div class="w-14 h-14 rounded-2xl bg-sky-500 flex items-center justify-center text-white shadow-lg shadow-sky-500/20">
                    <i data-lucide="pencil" class="w-7 h-7"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">Edit Workstation</h2>
                    <p class="text-[13px] opacity-60" x-text="'Modify properties for ' + editWorkstation.name"></p>
                </div>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest mb-2 opacity-50">New Name</label>
                    <input type="text" x-model="editWorkstation.name" class="w-full h-12 px-4 rounded-xl border bg-transparent font-medium focus:ring-4 focus:ring-sky-500/10 transition-all outline-none" :class="theme === 'perfectlum' ? 'border-gray-200 bg-gray-50' : 'border-white/10 bg-white/5'">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest mb-2 opacity-50">Workgroup</label>
                    <select class="w-full h-12 px-4 rounded-xl border bg-transparent font-medium focus:ring-4 focus:ring-sky-500/10 transition-all outline-none" :class="theme === 'perfectlum' ? 'border-gray-200 bg-gray-50' : 'border-white/10 bg-white/5'">
                        <option>Qs-group test</option>
                        <option>Main Hospital</option>
                        <option>Radiology Center</option>
                    </select>
                </div>
                <div class="pt-6 flex gap-3">
                    <button @click="backFromWorkstationAction()" class="px-8 h-12 rounded-xl bg-sky-500 text-white font-bold shadow-lg shadow-sky-500/20 transition-transform active:scale-95">Save Changes</button>
                    <button @click="backFromWorkstationAction()" class="px-8 h-12 rounded-xl border dark:border-white/10 font-bold">Discard</button>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 3: SETTINGS WORKSTATION (In-Pane) -->
    <div x-show="workgroupViewState === 'settings-workstation'" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-x-8" 
         x-transition:enter-end="opacity-100 translate-x-0"
         class="py-4">
        <button @click="backFromWorkstationAction()" class="group flex items-center gap-2 text-violet-500 font-bold text-[13px] mb-8 hover:translate-x-[-4px] transition-transform">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> <span x-text="workgroupModalContext === 'workstations' ? 'Cancel' : 'Back to Workgroup'"></span>
        </button>
        
        <div class="bg-white dark:bg-[#111216] rounded-2xl border overflow-hidden shadow-2xl" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
            <!-- Settings Header -->
            <div class="p-8 border-b dark:border-white/10 flex items-center justify-between bg-gradient-to-r"
                 :class="theme === 'perfectlum' ? 'from-white to-violet-50/30' : 'from-[#111216] to-violet-900/5'">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-violet-600 flex items-center justify-center text-white shadow-lg shadow-violet-600/20">
                        <i data-lucide="settings" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold" x-text="workstationSettingsName"></h2>
                        <span class="text-[12px] font-bold text-violet-500 uppercase tracking-widest">Configuration Console</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button @click="backFromWorkstationAction()" class="px-6 h-11 rounded-xl bg-violet-600 text-white font-bold text-[13px] shadow-lg shadow-violet-600/20 active:scale-95 transition-all">Apply All</button>
                </div>
            </div>
            
            <!-- Settings Shell -->
            <div class="flex h-[500px]">
                <!-- Side Tabs -->
                <div class="w-64 border-r dark:border-white/10 bg-gray-50/30 dark:bg-white/5 p-4 space-y-2">
                    <template x-for="tab in [{id:'application', label:'Application', icon:'app-window'}, {id:'display', label:'Display Calib.', icon:'monitor'}, {id:'qa', label:'Quality Assurance', icon:'shield-check'}, {id:'location', label:'Location', icon:'map-pin'}]">
                        <button @click="workstationSettingsTab = tab.id" 
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-[13px] transition-all"
                                :class="workstationSettingsTab === tab.id ? 'bg-violet-600 text-white shadow-lg shadow-violet-600/20' : 'opacity-60 hover:opacity-100 hover:bg-black/5 dark:hover:bg-white/5'">
                            <i :data-lucide="tab.icon" class="w-4 h-4"></i>
                            <span x-text="tab.label"></span>
                        </button>
                    </template>
                </div>
                
                <!-- Content Area -->
                <div class="flex-1 p-10 overflow-y-auto custom-scrollbar">
                    <div x-show="workstationSettingsTab === 'application'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[12px] opacity-70 mb-1 block">Workgroup</label>
                                <select class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                    <option>Qs-group test</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[12px] opacity-70 mb-1 block">Units of Length</label>
                                <select class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                    <option>centimetres</option>
                                    <option>inches</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[12px] opacity-70 mb-1 block">Units of Luminance</label>
                                <select class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                    <option>cd/m²</option>
                                    <option>fL</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[12px] opacity-70 mb-1 block">Veiling Luminance</label>
                                <div class="relative">
                                    <input type="text" value="0.763" class="w-full h-11 pl-4 pr-12 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[12px] opacity-50">cd/m²</span>
                                </div>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-[12px] opacity-70 mb-1 block">Ambient Conditions Stable</label>
                                <select class="w-full md:w-1/2 h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                    <option>yes</option>
                                    <option>no</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2" x-data="{ energySave: false }">
                            <button type="button" @click="energySave = !energySave" class="w-10 h-5 rounded-full relative flex items-center shrink-0 transition-colors" :class="energySave ? 'bg-sky-500' : 'bg-gray-300 dark:bg-white/10'">
                                <div class="absolute left-1 w-3 h-3 rounded-full bg-white shadow-sm transition-transform" :class="energySave ? 'translate-x-5' : 'translate-x-0'"></div>
                            </button>
                            <span class="text-[13px] font-medium">Enable Display Energy Save Mode</span>
                        </div>

                        <div class="pt-4">
                            <button class="px-6 h-10 rounded-full bg-[#0ea5e9] hover:bg-sky-600 text-white font-medium text-[13px] transition-colors shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- Display Calibration Tab Content -->
                    <div x-show="workstationSettingsTab === 'display'" class="space-y-6" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Preset</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[12px] opacity-70 block">Color Temperature</label>
                                    <select class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                        <option>custom</option>
                                    </select>
                                    <input type="text" placeholder="Enter custom value" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Max Luminance (FL)</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Luminance Response</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Max Luminance (FL)</label>
                                    <div class="relative">
                                        <select class="w-full h-11 pl-4 pr-12 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all appearance-none" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                            <option>Select</option>
                                        </select>
                                        <div class="pointer-events-none absolute right-0 inset-y-0 flex items-center pr-4">
                                            <span class="text-[12px] opacity-50">fl</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Gamut</label>
                                    <div class="relative">
                                        <select class="w-full h-11 pl-4 pr-12 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all appearance-none" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                            <option>Native</option>
                                        </select>
                                        <div class="pointer-events-none absolute right-0 inset-y-0 flex items-center pr-4">
                                            <span class="text-[12px] opacity-50">fl</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-sky-500 focus:ring-sky-500/20 transition-all bg-transparent" :class="theme === 'perfectlum' ? 'border-gray-300' : 'border-white/20 bg-black/20'">
                                <span class="text-[13px] font-medium">Create Display ICC Profile</span>
                            </label>
                        </div>

                        <div class="pt-4">
                            <button class="px-6 h-10 rounded-full bg-[#0ea5e9] hover:bg-sky-600 text-white font-medium text-[13px] transition-colors shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- Quality Assurance Tab Content -->
                    <div x-show="workstationSettingsTab === 'qa'" class="space-y-6" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Regulation</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Body-Region</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Room Class</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-sky-500 focus:ring-sky-500/20 transition-all bg-transparent" :class="theme === 'perfectlum' ? 'border-gray-300' : 'border-white/20 bg-black/20'">
                                <span class="text-[13px] font-medium">Start daily tests automatically</span>
                            </label>
                        </div>

                        <div class="pt-4">
                            <button class="px-6 h-10 rounded-full bg-[#0ea5e9] hover:bg-sky-600 text-white font-medium text-[13px] transition-colors shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- Location Tab Content -->
                    <div x-show="workstationSettingsTab === 'location'" class="space-y-6" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Facility</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Room</label>
                                    <input type="text" value="2" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Address</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Email</label>
                                    <input type="email" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Department</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Responsible Person</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">City</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[12px] opacity-70 mb-1 block">Phone Number</label>
                                    <input type="text" class="w-full h-11 px-4 rounded-xl border bg-transparent font-medium text-[13px] outline-none focus:ring-2 transition-all" :class="theme === 'perfectlum' ? 'border-gray-200 hover:border-gray-300 focus:border-sky-500 focus:ring-sky-500/20' : 'border-white/10 hover:border-white/20 focus:border-sky-500 focus:ring-sky-500/20 text-white'">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button class="px-6 h-10 rounded-full bg-[#0ea5e9] hover:bg-sky-600 text-white font-medium text-[13px] transition-colors shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 5: DELETE WORKSTATION CONFIRMATION (In-Pane) -->
    <div x-show="workgroupViewState === 'delete-workstation'" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0 translate-x-8" 
         x-transition:enter-end="opacity-100 translate-x-0"
         class="max-w-xl mx-auto py-10">
        <button @click="backFromWorkstationAction()" class="group flex items-center gap-2 text-rose-500 font-bold text-[13px] mb-8 hover:translate-x-[-4px] transition-transform">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> <span x-text="workgroupModalContext === 'workstations' ? 'Cancel' : 'Back to Workgroup'"></span>
        </button>
        
        <div class="bg-white dark:bg-[#111216] rounded-2xl border p-8 shadow-xl" :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/10'">
            <div class="flex items-center gap-5 mb-10 border-b pb-8" :class="theme === 'perfectlum' ? 'border-gray-100' : 'border-white/10'">
                <div class="w-14 h-14 rounded-2xl bg-rose-500 flex items-center justify-center text-white shadow-lg shadow-rose-500/20">
                    <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-rose-500">Delete Workstation?</h2>
                    <p class="text-[13px] opacity-60">This action cannot be undone.</p>
                </div>
            </div>
            
            <div class="space-y-6 text-center">
                <div class="p-6 rounded-xl" :class="theme === 'perfectlum' ? 'bg-rose-50' : 'bg-rose-500/10'">
                    <p class="text-[14px] mb-2 font-medium" :class="theme === 'perfectlum' ? 'text-rose-900' : 'text-rose-200'">You are about to permanently delete:</p>
                    <p class="text-[18px] font-bold text-rose-500" x-text="deleteWorkstationTarget"></p>
                </div>
                
                <p class="text-[13px] opacity-70">All displays, calibration history, and configuration settings associated with this workstation will be permanently removed from the system.</p>
                
                <div class="pt-6 flex gap-3 justify-center">
                    <button @click="backFromWorkstationAction(); /* Add actual delete logic here */" class="px-8 h-12 rounded-xl bg-rose-500 text-white font-bold shadow-lg shadow-rose-500/20 transition-transform active:scale-95">Yes, Delete Workstation</button>
                    <button @click="backFromWorkstationAction()" class="px-8 h-12 rounded-xl border dark:border-white/10 font-bold">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</div>
