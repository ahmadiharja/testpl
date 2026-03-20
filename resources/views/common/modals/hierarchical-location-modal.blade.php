<!-- HIERARCHICAL LOCATION MODAL SYSTEM (Alpine.js) -->
<div x-data="hierarchyModal()" 
     @open-hierarchy.window="open($event.detail)"
     class="font-inter">
     
    <!-- OVERLAY -->
    <div x-cloak x-show="isOpen" 
         x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[9000]" 
         @click="close()"></div>

    <!-- 1. FACILITY: CENTERED MODAL -->
    <div x-cloak x-show="isOpen && current.type === 'facility'" 
         class="fixed inset-0 z-[9020] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
         
        <div x-cloak x-show="isOpen && current.type === 'facility'"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-8"
             @click.stop
             class="bg-[#F4F6F9] w-full max-w-5xl rounded-[1.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] pointer-events-auto flex flex-col relative"
             style="height: 85vh;">
            
            <!-- Global Nav Buttons -->
            <div class="absolute top-6 right-6 flex items-center gap-3 z-50">
                <button x-show="viewStack.length > 1" @click="popView()" class="p-2 rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors backdrop-blur-sm" title="{{ __('Go Back') }}">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </button>
                <button x-show="current.type === 'display'" @click="openDisplayStructureMap()" class="inline-flex items-center gap-2 rounded-full bg-black/10 hover:bg-black/20 px-4 py-2 text-xs font-semibold text-white transition-colors backdrop-blur-sm" title="{{ __('Open structure map') }}">
                    <i data-lucide="workflow" class="w-4 h-4"></i>
                    {{ __('Map') }}
                </button>
                <button x-show="current.type === 'workgroup'" @click="openWorkgroupStructureMap()" class="inline-flex items-center gap-2 rounded-full bg-black/10 hover:bg-black/20 px-4 py-2 text-xs font-semibold text-white transition-colors backdrop-blur-sm" title="{{ __('Open workgroup map') }}">
                    <i data-lucide="workflow" class="w-4 h-4"></i>
                    {{ __('Map') }}
                </button>
                <button x-show="current.type === 'workstation'" @click="openWorkstationStructureMap()" class="inline-flex items-center gap-2 rounded-full bg-black/10 hover:bg-black/20 px-4 py-2 text-xs font-semibold text-white transition-colors backdrop-blur-sm" title="{{ __('Open workstation map') }}">
                    <i data-lucide="workflow" class="w-4 h-4"></i>
                    {{ __('Map') }}
                </button>
                <button @click="close()" class="p-2 rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors backdrop-blur-sm" title="{{ __('Close') }}">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- ============================== -->
            <!-- 1. FACILITY VIEW -->
            <!-- ============================== -->
            <template x-if="current.type === 'facility'">
                <div class="flex flex-col h-full w-full">
                    <!-- Header -->
                    <div class="relative bg-gradient-to-r from-[#1175FF] to-[#0A62F0] rounded-t-[1.5rem] px-10 py-8 shrink-0 overflow-hidden">
                        <!-- Dot Pattern -->
                        <div class="absolute inset-0 z-0 opacity-[0.25] pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 1) 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
                        
                        <div class="flex items-center gap-5 relative z-10 text-white">
                            <div class="w-[3.5rem] h-[3.5rem] rounded-[1rem] bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-sm shrink-0 shadow-inner">
                                <i data-lucide="building-2" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-widest opacity-90 mb-0.5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1)">{{ __('Facility Details') }}</p>
                                <h2 class="text-[32px] leading-tight font-extrabold tracking-tight drop-shadow-sm truncate" x-text="facilityDetail?.name || @js(__('Loading facility…'))">{{ __('Loading facility…') }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-10 space-y-8 no-scrollbar relative z-0">
                        <template x-if="facilityLoading">
                            <div class="flex h-full min-h-[20rem] items-center justify-center rounded-[1.5rem] bg-white">
                                <div class="flex flex-col items-center gap-4 text-slate-500">
                                    <div class="h-12 w-12 rounded-full border-4 border-sky-200 border-t-sky-500 animate-spin"></div>
                                    <p class="text-sm font-semibold">{{ __('Loading facility details...') }}</p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!facilityLoading && facilityError">
                            <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 p-8 text-center">
                                <p class="text-sm font-bold uppercase tracking-[0.2em] text-rose-500">{{ __('Unable To Load') }}</p>
                                <p class="mt-3 text-sm text-rose-700" x-text="facilityError"></p>
                            </div>
                        </template>
                        <template x-if="!facilityLoading && !facilityError && facilityDetail">
                            <div class="space-y-8">
                                <div class="bg-white rounded-[1.5rem] p-8 border border-gray-100 shadow-sm">
                                    <div class="flex items-center justify-between mb-8">
                                        <h3 class="text-lg font-extrabold text-gray-900">{{ __('Facility Details') }}</h3>
                                        <div class="flex items-center gap-3">
                                            <template x-if="!facilityEditing && facilityDetail?.permissions?.edit">
                                                <button type="button" class="text-sm font-bold text-sky-500 hover:text-sky-600 flex items-center gap-2" @click="beginFacilityEdit()"><i data-lucide="pen-line" class="w-4 h-4"></i> {{ __('Edit') }}</button>
                                            </template>
                                            <template x-if="facilityEditing">
                                                <div class="flex items-center gap-3">
                                                    <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cancelFacilityEdit()"><i data-lucide="x" class="h-4 w-4"></i>{{ __('Cancel') }}</button>
                                                    <button type="button" class="inline-flex items-center gap-2 rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingFacility" @click="confirmSaveFacility()"><i data-lucide="save" class="h-4 w-4"></i><span x-text="savingFacility ? @js(__('Saving…')) : @js(__('Save Changes'))"></span></button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <template x-if="!facilityEditing">
                                        <div class="grid gap-6 md:grid-cols-2">
                                            <div>
                                                <p class="text-[11px] font-bold text-gray-400 mb-2">{{ __('Facility Name') }}</p>
                                                <p class="text-[13px] font-bold text-gray-800" x-text="facilityDetail.name || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-bold text-gray-400 mb-2">{{ __('Timezone') }}</p>
                                                <p class="text-[13px] font-bold text-gray-800" x-text="facilityDetail.timezone || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-bold text-gray-400 mb-2">{{ __('Description') }}</p>
                                                <p class="text-[13px] font-bold text-gray-800" x-text="facilityDetail.description || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-bold text-gray-400 mb-2">{{ __('Location') }}</p>
                                                <p class="text-[13px] font-bold text-gray-800" x-text="facilityDetail.location || '-'"></p>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="facilityEditing">
                                        <div class="grid gap-5 md:grid-cols-2">
                                            <div>
                                                <label class="mb-1.5 block text-sm font-semibold text-slate-600">{{ __('Facility Name') }}</label>
                                                <input type="text" x-model="facilityForm.name" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                            </div>
                                            <div>
                                                <label class="mb-1.5 block text-sm font-semibold text-slate-600">{{ __('Timezone') }}</label>
                                                <input type="text" x-model="facilityForm.timezone" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1.5 block text-sm font-semibold text-slate-600">{{ __('Description') }}</label>
                                                <textarea x-model="facilityForm.description" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1.5 block text-sm font-semibold text-slate-600">{{ __('Location') }}</label>
                                                <input type="text" x-model="facilityForm.location" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div>
                                    <h3 class="text-[15px] font-extrabold text-gray-900 flex items-center gap-2 mb-6"><i data-lucide="network" class="w-5 h-5 text-gray-400"></i> {{ __('Registered Workgroups') }}</h3>
                                    <div class="bg-white border border-gray-100 rounded-[1.5rem] shadow-sm p-2">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="border-b border-gray-100">
                                                    <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-sky-600">Name</th>
                                                    <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-sky-600">Address</th>
                                                    <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-sky-600">Phone</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="item in facilityDetail.workgroups" :key="`facility-workgroup-${item.id}`">
                                                    <tr @click="pushView('workgroup', item.id)" class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors cursor-pointer group last:border-b-0">
                                                        <td class="px-6 py-5 text-[13px] font-bold text-sky-500 group-hover:underline flex items-center gap-2"><i data-lucide="network" class="w-4 h-4"></i><span x-text="item.name"></span></td>
                                                        <td class="px-6 py-5 text-[13px] font-semibold text-gray-500" x-text="item.address"></td>
                                                        <td class="px-6 py-5 text-[13px] font-semibold text-gray-500" x-text="item.phone"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

        </div>
    </div>

    <!-- 2. WORKGROUP / WORKSTATION / DISPLAY: SIDE PANEL DRAWER -->
    <div x-cloak x-show="isOpen && current.type !== 'facility'" 
         class="fixed inset-0 z-[9030] flex justify-end pointer-events-none">
         
        <!-- Drawer Panel -->
        <div x-cloak x-show="isOpen && current.type !== 'facility'"
             x-transition:enter="transform transition ease-out duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-400"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             @click.stop
             class="h-full rounded-l-[1.5rem] bg-[#F4F6F9] shadow-[-20px_0_60px_-15px_rgba(0,0,0,0.3)] pointer-events-auto flex flex-col relative overflow-hidden"
             style="width: min(1080px, calc(100vw - 120px));">
            
            <!-- Global Nav Buttons for Drawer -->
            <div class="absolute top-6 right-6 flex items-center gap-3 z-50">
                <button x-show="viewStack.length > 1" @click="popView()" class="p-2 rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors backdrop-blur-sm" title="{{ __('Go Back') }}">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </button>
                <button x-show="current.type === 'display'" @click="openDisplayStructureMap()" class="inline-flex items-center gap-2 rounded-full bg-black/10 hover:bg-black/20 px-4 py-2 text-xs font-semibold text-white transition-colors backdrop-blur-sm" title="{{ __('Open structure map') }}">
                    <i data-lucide="workflow" class="w-4 h-4"></i>
                    {{ __('Map') }}
                </button>
                <button x-show="current.type === 'workgroup'" @click="openWorkgroupStructureMap()" class="inline-flex items-center gap-2 rounded-full bg-black/10 hover:bg-black/20 px-4 py-2 text-xs font-semibold text-white transition-colors backdrop-blur-sm" title="{{ __('Open workgroup map') }}">
                    <i data-lucide="workflow" class="w-4 h-4"></i>
                    {{ __('Map') }}
                </button>
                <button x-show="current.type === 'workstation'" @click="openWorkstationStructureMap()" class="inline-flex items-center gap-2 rounded-full bg-black/10 hover:bg-black/20 px-4 py-2 text-xs font-semibold text-white transition-colors backdrop-blur-sm" title="{{ __('Open workstation map') }}">
                    <i data-lucide="workflow" class="w-4 h-4"></i>
                    {{ __('Map') }}
                </button>
                <button @click="close()" class="p-2 rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors backdrop-blur-sm" title="{{ __('Close') }}">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- ============================== -->
            <!-- 2. WORKGROUP VIEW -->
            <!-- ============================== -->
            <template x-if="current.type === 'workgroup'">
                <div class="flex flex-col h-full w-full">
                    <div class="relative bg-gradient-to-r from-[#1175FF] to-[#0A62F0] px-8 py-6 shrink-0 overflow-hidden border-b border-[#0A62F0]/50">
                        <div class="absolute inset-0 z-0 opacity-[0.25] pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 1) 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
                        <div class="flex items-center gap-5 relative z-10 text-white">
                            <div class="w-[3.5rem] h-[3.5rem] rounded-[1rem] bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-sm shrink-0 shadow-inner">
                                <i data-lucide="network" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-widest opacity-90 mb-0.5">{{ __('Workgroup Details') }}</p>
                                <h2 class="text-[32px] leading-tight font-extrabold tracking-tight drop-shadow-sm truncate" x-text="workgroupDetail?.name || @js(__('Loading workgroup…'))">{{ __('Loading workgroup…') }}</h2>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto w-full no-scrollbar relative z-0 flex">
                        <template x-if="workgroupLoading">
                            <div class="flex h-full w-full items-center justify-center bg-white">
                                <div class="flex flex-col items-center gap-4 text-slate-500">
                                    <div class="h-12 w-12 rounded-full border-4 border-sky-200 border-t-sky-500 animate-spin"></div>
                                    <p class="text-sm font-semibold">{{ __('Loading workgroup details...') }}</p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!workgroupLoading && workgroupError">
                            <div class="flex h-full w-full items-center justify-center bg-white p-10">
                                <div class="max-w-md rounded-[1.5rem] border border-rose-200 bg-rose-50 p-8 text-center">
                                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-rose-500">{{ __('Unable To Load') }}</p>
                                    <p class="mt-3 text-sm text-rose-700" x-text="workgroupError"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!workgroupLoading && !workgroupError && workgroupDetail">
                            <div class="flex h-full w-full">
                                <div class="w-72 bg-white border-r border-gray-100 p-6 shrink-0 space-y-6">
                                    <div>
                                        <h4 class="text-[15px] font-extrabold text-gray-900">{{ __('Workgroup Hierarchy') }}</h4>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="mb-1 text-[10px] font-semibold text-slate-500">{{ __('Facility:') }}</p>
                                            <button type="button" @click="pushView('facility', workgroupDetail.facility.id)" class="flex h-12 w-full items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 text-left text-[14px] font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 cursor-pointer" x-text="workgroupDetail.facility.name"></button>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-[10px] font-semibold text-slate-500">{{ __('Workgroup:') }}</p>
                                            <div class="flex min-h-[3rem] w-full items-center rounded-2xl border border-sky-300 bg-sky-50 px-4 py-2 text-[14px] font-bold text-sky-700" x-text="workgroupDetail.name"></div>
                                        </div>
                                    </div>
                                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Contact') }}</p>
                                        <div class="mt-4 space-y-4 text-sm text-slate-700">
                                            <div>
                                                <p class="text-[11px] font-semibold text-slate-500">{{ __('Address') }}</p>
                                                <p class="mt-2 font-semibold text-slate-900" x-text="workgroupDetail.address || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-semibold text-slate-500">{{ __('Phone Number') }}</p>
                                                <p class="mt-2 font-semibold text-slate-900" x-text="workgroupDetail.phone || '-'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0 bg-[#F7F9FC] overflow-y-auto">
                                    <div class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 px-8 pt-6 backdrop-blur">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="inline-flex items-center gap-3 border-b border-slate-100">
                                                <button type="button" @click="activeWorkgroupTab = 'overview'" class="px-1 py-3 text-[15px] font-semibold transition border-b-2" :class="activeWorkgroupTab === 'overview' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'">{{ __('Overview') }}</button>
                                                <button type="button" @click="activeWorkgroupTab = 'settings'" class="px-1 py-3 text-[15px] font-semibold transition border-b-2" :class="activeWorkgroupTab === 'settings' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'">{{ __('Settings') }}</button>
                                            </div>
                                            <div class="flex items-center gap-3" x-show="activeWorkgroupTab === 'settings'">
                                                <template x-if="!workgroupSettingsEditing && workgroupDetail?.permissions?.edit">
                                                    <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600" @click="beginWorkgroupSettingsEdit()">
                                                        <i data-lucide="pen-line" class="h-4 w-4"></i>{{ __('Edit') }}
                                                    </button>
                                                </template>
                                                <template x-if="workgroupSettingsEditing">
                                                    <div class="flex items-center gap-3">
                                                        <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="cancelWorkgroupSettingsEdit()">
                                                            <i data-lucide="x" class="h-4 w-4"></i>{{ __('Cancel') }}
                                                        </button>
                                                        <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingWorkgroupSettings" @click="confirmSaveWorkgroupSettings()">
                                                            <i data-lucide="save" class="h-4 w-4"></i>
                                                            <span x-text="savingWorkgroupSettings ? @js(__('Saving...')) : @js(__('Save Changes'))"></span>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-8 p-8">
                                        <template x-if="activeWorkgroupTab === 'overview'">
                                            <div class="space-y-8">
                                                <div class="grid gap-6 md:grid-cols-2">
                                                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">{{ __('Workstations') }}</p>
                                                        <p class="mt-5 text-[3rem] font-black tracking-tight text-slate-900" x-text="workgroupDetail.summary.workstationCount"></p>
                                                    </div>
                                                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">{{ __('Displays') }}</p>
                                                        <p class="mt-5 text-[3rem] font-black tracking-tight text-slate-900" x-text="workgroupDetail.summary.displayCount"></p>
                                                    </div>
                                                    <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 p-5 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-emerald-600">{{ __('Healthy') }}</p>
                                                        <p class="mt-5 text-[3rem] font-black tracking-tight text-emerald-600" x-text="workgroupDetail.summary.healthyCount"></p>
                                                    </div>
                                                    <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 p-5 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-rose-600">{{ __('Needs Attention') }}</p>
                                                        <p class="mt-5 text-[3rem] font-black tracking-tight text-rose-600" x-text="workgroupDetail.summary.attentionCount"></p>
                                                    </div>
                                                </div>

                                                <div class="space-y-6">
                                                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                        <div class="flex items-center justify-between gap-4">
                                                            <div>
                                                                <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">{{ __('Registered Workstations') }}</p>
                                                                <h3 class="mt-3 text-[2rem] leading-[1.05] font-black tracking-tight text-slate-900">{{ __('All workstations in this workgroup') }}</h3>
                                                            </div>
                                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500" x-text="`${workgroupDetail.workstations.length} workstations`"></span>
                                                        </div>
                                                        <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200">
                                                            <table class="min-w-full divide-y divide-slate-200">
                                                                <thead class="bg-slate-50">
                                                                    <tr>
                                                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Workstation') }}</th>
                                                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Displays') }}</th>
                                                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Status') }}</th>
                                                                        <th class="px-5 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Last Connected') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="divide-y divide-slate-100 bg-white">
                                                                    <template x-for="item in workgroupDetail.workstations" :key="`wg-workstation-${item.id}`">
                                                                        <tr class="transition hover:bg-sky-50/50">
                                                                            <td class="px-5 py-4">
                                                                                <button type="button" class="text-left text-sm font-bold text-sky-600 transition hover:text-sky-700 hover:underline" @click="pushView('workstation', item.id)" x-text="item.name"></button>
                                                                            </td>
                                                                            <td class="px-5 py-4 text-sm font-semibold text-slate-700" x-text="item.displayCount"></td>
                                                                            <td class="px-5 py-4">
                                                                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold" :class="item.attentionCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'">
                                                                                    <span class="inline-block h-2 w-2 rounded-full" :class="item.attentionCount > 0 ? 'bg-rose-500' : 'bg-emerald-500'"></span>
                                                                                    <span x-text="item.attentionCount > 0 ? @js(__('Needs Attention')) : @js(__('Healthy'))"></span>
                                                                                </span>
                                                                            </td>
                                                                            <td class="px-5 py-4 text-sm font-semibold text-slate-700" x-text="item.lastConnected"></td>
                                                                        </tr>
                                                                    </template>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-slate-400">{{ __('Workgroup Summary') }}</p>
                                                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Facility') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupDetail.facility.name"></p></div>
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Workgroup') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupDetail.name"></p></div>
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Address') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupDetail.address || '-'"></p></div>
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Phone Number') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupDetail.phone || '-'"></p></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="activeWorkgroupTab === 'settings'">
                                            <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_12px_35px_rgba(15,23,42,0.08)]">
                                                <template x-if="!workgroupSettingsEditing">
                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Workgroup Name') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupSettingsForm.name || '-'"></p></div>
                                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Facility') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="selectedWorkgroupFacilityName() || '-'"></p></div>
                                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Address') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupSettingsForm.address || '-'"></p></div>
                                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">{{ __('Phone Number') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workgroupSettingsForm.phone || '-'"></p></div>
                                                    </div>
                                                </template>
                                                <template x-if="workgroupSettingsEditing">
                                                    <div class="grid gap-5 sm:grid-cols-2">
                                                        <div>
                                                            <label class="mb-1.5 block text-sm font-semibold text-slate-600">Workgroup Name</label>
                                                            <input type="text" x-model="workgroupSettingsForm.name" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1.5 block text-sm font-semibold text-slate-600">Facility</label>
                                                            <div class="relative" @click.outside="closeInlineSelect()">
                                                                <button type="button" :disabled="!workgroupDetail?.permissions?.changeFacility" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400" @click="toggleInlineSelect('workgroup-settings-facility')">
                                                                    <span class="truncate" x-text="selectedWorkgroupFacilityName() || 'Select facility'"></span>
                                                                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                                </button>
                                                                <div x-show="isInlineSelectOpen('workgroup-settings-facility')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                                    <input x-ref="search-workgroup-settings-facility" x-model="workgroupOptionSearch.facilities" type="text" placeholder="Search facilities..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                                    <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workgroupFacilityOptionHint()"></p>
                                                                    <div class="max-h-56 space-y-1 overflow-y-auto">
                                                                        <template x-for="facility in filteredWorkgroupFacilityOptions()" :key="`wg-setting-facility-${facility.id}`">
                                                                            <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-[12px] font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workgroupSettingsForm.facility_id = String(facility.id); closeInlineSelect()">
                                                                                <span class="truncate" x-text="facility.name"></span>
                                                                            </button>
                                                                        </template>
                                                                        <template x-if="!filteredWorkgroupFacilityOptions().length">
                                                                            <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label class="mb-1.5 block text-sm font-semibold text-slate-600">Address</label>
                                                            <textarea x-model="workgroupSettingsForm.address" rows="4" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></textarea>
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label class="mb-1.5 block text-sm font-semibold text-slate-600">Phone Number</label>
                                                            <input type="text" x-model="workgroupSettingsForm.phone" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ============================== -->
            <!-- 3. WORKSTATION VIEW -->
            <!-- ============================== -->
            <template x-if="current.type === 'workstation'">
                <div class="flex flex-col h-full w-full">
                    <!-- Header -->
                    <div class="relative bg-gradient-to-r from-[#1175FF] to-[#0A62F0] px-8 py-6 shrink-0 overflow-hidden border-b border-[#0A62F0]/50">
                        <!-- Dot Pattern -->
                        <div class="absolute inset-0 z-0 opacity-[0.25] pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 1) 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
                        <div class="flex items-center gap-5 relative z-10 text-white">
                            <div class="w-[3.5rem] h-[3.5rem] rounded-[1rem] bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-sm shrink-0 shadow-inner">
                                <i data-lucide="server" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-widest opacity-90 mb-0.5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1)">WORKSTATION DETAILS</p>
                                <h2 class="text-[32px] leading-tight font-extrabold tracking-tight drop-shadow-sm truncate" x-text="workstationDetail?.name || @js(__('Loading workstation…'))">{{ __('Loading workstation…') }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto w-full no-scrollbar relative z-0 flex">
                        <template x-if="workstationLoading">
                            <div class="flex h-full w-full items-center justify-center bg-white">
                                <div class="flex flex-col items-center gap-4 text-slate-500">
                                    <div class="h-12 w-12 rounded-full border-4 border-sky-200 border-t-sky-500 animate-spin"></div>
                                    <p class="text-sm font-semibold">{{ __('Loading workstation details...') }}</p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!workstationLoading && workstationError">
                            <div class="flex h-full w-full items-center justify-center bg-white p-10">
                                <div class="max-w-md rounded-[1.5rem] border border-rose-200 bg-rose-50 p-8 text-center">
                                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-rose-500">{{ __('Unable To Load') }}</p>
                                    <p class="mt-3 text-sm text-rose-700" x-text="workstationError"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="!workstationLoading && !workstationError && workstationDetail">
                            <div class="flex h-full w-full">
                                <!-- Left sidebar (hierarchy/meta) -->
                                <div class="w-72 bg-white border-r border-gray-100 p-6 shrink-0 space-y-6">
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="text-[15px] font-extrabold text-gray-900">{{ __('Workstation Hierarchy') }}</h4>
                                        <button type="button" :disabled="!workstationDetail?.permissions?.changeWorkgroup" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" @click="openWorkstationHierarchyEdit()">
                                            <i data-lucide="move-horizontal" class="h-4 w-4"></i>Move
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="mb-1 text-[10px] font-semibold text-slate-500">Facility:</p>
                                            <button type="button" @click="pushView('facility', workstationDetail.facility.id)" class="flex h-12 w-full items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 text-left text-[14px] font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 cursor-pointer" x-text="workstationDetail.facility.name"></button>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-[10px] font-semibold text-slate-500">Workgroup:</p>
                                            <button type="button" @click="pushView('workgroup', workstationDetail.workgroup.id)" class="flex h-12 w-full items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 text-left text-[14px] font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 cursor-pointer" x-text="workstationDetail.workgroup.name"></button>
                                        </div>
                                        <div>
                                            <p class="mb-1 text-[10px] font-semibold text-slate-500">Workstation:</p>
                                            <div class="flex h-12 w-full items-center rounded-2xl border border-sky-300 bg-sky-50 px-4 text-[14px] font-bold text-sky-700" x-text="workstationDetail.name"></div>
                                        </div>
                                    </div>
                                    <div x-show="showWorkstationHierarchyEdit" x-cloak class="rounded-[1.5rem] border border-slate-200 bg-white p-4 shadow-sm">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Move Workstation') }}</p>
                                        <p class="mt-2 text-[12px] leading-5 text-slate-500">Relocate this workstation by selecting a new facility and workgroup.</p>
                                        <div class="mt-4 space-y-3">
                                            <div>
                                                <label class="mb-1.5 block text-[10px] font-semibold text-slate-500">Facility</label>
                                                <div class="relative" @click.outside="closeInlineSelect()">
                                                    <button type="button" class="flex h-10 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" @click="toggleInlineSelect('workstation-move-facilities')">
                                                        <span class="truncate" x-text="selectedWorkstationFacilityName() || @js(__('Select facility'))"></span>
                                                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                    </button>
                                                    <div x-show="isInlineSelectOpen('workstation-move-facilities')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                        <input x-ref="search-workstation-move-facilities" x-model="workstationMoveSearch.facilities" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                        <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationMoveOptionHint('facilities')"></p>
                                                        <div class="max-h-56 space-y-1 overflow-y-auto">
                                                            <template x-for="facility in filteredWorkstationMoveOptions('facilities')" :key="`ws-move-facility-${facility.id}`">
                                                                <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-[12px] font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationMoveForm.facilityId = String(facility.id); closeInlineSelect(); changeWorkstationMoveFacility()">
                                                                    <span class="truncate" x-text="facility.name"></span>
                                                                </button>
                                                            </template>
                                                            <template x-if="!filteredWorkstationMoveOptions('facilities').length">
                                                                <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="mb-1.5 block text-[10px] font-semibold text-slate-500">Workgroup</label>
                                                <div class="relative" @click.outside="closeInlineSelect()">
                                                    <button type="button" :disabled="!workstationMoveForm.facilityId || workstationMoveLoading" class="flex h-10 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400" @click="toggleInlineSelect('workstation-move-workgroups')">
                                                        <span class="truncate" x-text="selectedWorkstationWorkgroupName() || @js(__('Select workgroup'))"></span>
                                                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                    </button>
                                                    <div x-show="isInlineSelectOpen('workstation-move-workgroups')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                        <input x-ref="search-workstation-move-workgroups" x-model="workstationMoveSearch.workgroups" :disabled="!workstationMoveForm.facilityId || workstationMoveLoading" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400">
                                                        <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationMoveOptionHint('workgroups')"></p>
                                                        <div class="max-h-56 space-y-1 overflow-y-auto">
                                                            <template x-for="workgroup in filteredWorkstationMoveOptions('workgroups')" :key="`ws-move-workgroup-${workgroup.id}`">
                                                                <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-[12px] font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationMoveForm.workgroupId = String(workgroup.id); closeInlineSelect()">
                                                                    <span class="truncate" x-text="workgroup.name"></span>
                                                                </button>
                                                            </template>
                                                            <template x-if="!filteredWorkstationMoveOptions('workgroups').length">
                                                                <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-end gap-2">
                                            <button type="button" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50" @click="closeWorkstationHierarchyEdit()">{{ __('Cancel') }}</button>
                                            <button type="button" :disabled="!workstationMoveForm.workgroupId || workstationMoveLoading" class="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" @click="confirmWorkstationMove()"><span x-text="workstationMoveLoading ? @js(__('Moving…')) : @js(__('Move Workstation'))"></span></button>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100 border-dashed space-y-4">
                                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                            <p class="text-[11px] font-semibold text-slate-500">Last Connected</p>
                                            <p class="mt-2 text-[13px] font-bold text-slate-900" x-text="workstationDetail.lastConnected"></p>
                                            <p class="mt-4 text-[11px] font-semibold text-slate-500">Client Version</p>
                                            <p class="mt-2 text-[13px] font-bold text-slate-900 break-words" x-text="workstationDetail.clientVersion"></p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Right content -->
                                <div class="flex-1 overflow-y-auto bg-[#F8F9FB] no-scrollbar">
                                    <div class="sticky top-0 z-10 border-b border-slate-200 bg-[#F8F9FB] px-8 pt-6">
                                        <div class="flex items-center justify-between gap-4 border-b border-slate-200">
                                            <div class="flex items-center gap-2">
                                                <button type="button" class="border-b-2 px-3 py-3 text-sm font-semibold transition-colors" :class="activeWorkstationTab === 'overview' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" @click="activeWorkstationTab = 'overview'">{{ __('Overview') }}</button>
                                                <button type="button" class="border-b-2 px-3 py-3 text-sm font-semibold transition-colors" :class="activeWorkstationTab === 'settings' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'" @click="openWorkstationSettingsTab()">{{ __('Settings') }}</button>
                                            </div>
                                            <template x-if="activeWorkstationTab === 'settings'">
                                                <div class="flex items-center gap-2 pb-3">
                                                    <template x-if="!workstationSettingsEditing && workstationDetail?.permissions?.edit">
                                                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700" @click="beginWorkstationSettingsEdit()"><i data-lucide="pen-line" class="h-4 w-4"></i>{{ __('Edit') }}</button>
                                                    </template>
                                                    <template x-if="workstationSettingsEditing">
                                                        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50" @click="cancelWorkstationSettingsEdit()"><i data-lucide="x" class="h-4 w-4"></i>{{ __('Cancel') }}</button>
                                                    </template>
                                                    <template x-if="workstationSettingsEditing">
                                                        <button type="button" class="inline-flex items-center gap-2 rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingWorkstationSettings" @click="confirmSaveWorkstationSettings()"><i data-lucide="save" class="h-4 w-4"></i><span x-text="savingWorkstationSettings ? @js(__('Saving…')) : @js(__('Save Changes'))"></span></button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                        <template x-if="activeWorkstationTab === 'settings'">
                                            <div class="flex flex-wrap gap-2 pb-4 pt-4">
                                                <template x-for="tab in workstationSettingsTabs" :key="tab.key">
                                                    <button type="button" class="rounded-full px-4 py-2 text-sm font-semibold transition" :class="activeWorkstationSettingsTab === tab.key ? 'bg-sky-500 text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50'" @click="setWorkstationSettingsSubtab(tab.key)"><span x-text="tab.label"></span></button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="p-8 space-y-6">
                                        <template x-if="activeWorkstationTab === 'overview'">
                                            <div class="space-y-6">
                                                <div class="grid gap-4 xl:grid-cols-2">
                                                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                                                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">{{ __('Workstation Health') }}</p>
                                                        <div class="mt-4 grid grid-cols-2 gap-3">
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ __('Attached Displays') }}</p><p class="mt-3 text-3xl font-extrabold text-slate-900" x-text="workstationOverviewStats().totalDisplays"></p></div>
                                                            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-500">{{ __('Healthy') }}</p><p class="mt-3 text-3xl font-extrabold text-emerald-600" x-text="workstationOverviewStats().healthyDisplays"></p></div>
                                                            <div class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-rose-500">{{ __('Needs Attention') }}</p><p class="mt-3 text-3xl font-extrabold text-rose-600" x-text="workstationOverviewStats().attentionDisplays"></p></div>
                                                            <div class="rounded-2xl border border-sky-200 bg-sky-50/70 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-sky-500">{{ __('Client Version') }}</p><p class="mt-3 text-sm font-bold text-slate-900 break-words" x-text="workstationDetail.clientVersion"></p></div>
                                                        </div>
                                                    </div>
                                                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                                                        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400">{{ __('Hierarchy Summary') }}</p>
                                                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                                            <button type="button" @click="pushView('facility', workstationDetail.facility.id)" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-sky-200 hover:bg-sky-50/60"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Facility</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workstationDetail.facility.name"></p></button>
                                                            <button type="button" @click="pushView('workgroup', workstationDetail.workgroup.id)" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-sky-200 hover:bg-sky-50/60"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Workgroup</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workstationDetail.workgroup.name"></p></button>
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ __('Last Connected') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workstationDetail.lastConnected"></p></div>
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">{{ __('Workstation') }}</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="workstationDetail.name"></p></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                                    <div class="mb-5 flex items-center justify-between gap-4">
                                                        <h3 class="text-lg font-extrabold text-gray-900 mb-0 flex items-center gap-2"><i data-lucide="layers" class="w-5 h-5 text-gray-400"></i> {{ __('Attached Displays') }}</h3>
                                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500" x-text="`${workstationDetail.displays.length} displays`"></span>
                                                    </div>
                                                    <div class="overflow-hidden rounded-[1.25rem] border border-gray-100">
                                                        <table class="w-full text-left border-collapse">
                                                            <thead>
                                                                <tr class="bg-gray-50 border-b border-gray-100">
                                                                    <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-gray-400">Display Name</th>
                                                                    <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-gray-400">Model</th>
                                                                    <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-gray-400">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <template x-for="display in workstationDetail.displays" :key="`workstation-display-${display.id}`">
                                                                    <tr @click="pushView('display', display.id)" class="border-b border-gray-50 hover:bg-sky-50/50 cursor-pointer group">
                                                                        <td class="px-6 py-4"><span class="text-[13px] font-bold text-sky-500 group-hover:underline flex items-center gap-2"><i data-lucide="monitor" class="w-4 h-4"></i><span x-text="display.name"></span></span></td>
                                                                        <td class="px-6 py-4 text-[13px] font-semibold text-gray-600" x-text="display.model"></td>
                                                                        <td class="px-6 py-4"><span class="inline-flex py-1 px-2.5 rounded-md text-[11px] font-bold uppercase tracking-wider" :class="display.statusTone === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"><i :data-lucide="display.statusTone === 'success' ? 'check' : 'triangle-alert'" class="w-3 h-3 mr-1"></i><span x-text="display.statusLabel"></span></span></td>
                                                                    </tr>
                                                                </template>
                                                                <tr x-show="!workstationDetail.displays.length"><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500">{{ __('No displays are attached to this workstation.') }}</td></tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="activeWorkstationTab === 'settings'">
                                            <div class="space-y-6">
                                                <div x-show="workstationSettingsLoading" class="flex min-h-[22rem] items-center justify-center rounded-[1.5rem] border border-slate-200 bg-white"><div class="flex flex-col items-center gap-4 text-slate-500"><div class="h-12 w-12 rounded-full border-4 border-sky-200 border-t-sky-500 animate-spin"></div><p class="text-sm font-semibold">{{ __('Loading workstation settings...') }}</p></div></div>
                                                <template x-if="!workstationSettingsLoading && workstationSettingsError"><div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 p-6 text-sm text-rose-700" x-text="workstationSettingsError"></div></template>
                                                <div x-show="!workstationSettingsLoading && !workstationSettingsError && workstationSettingsReady" class="space-y-6">
                                                    <template x-if="!workstationSettingsEditing"><div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">This panel shows configuration values for the selected workstation. Use <span class="font-semibold text-slate-700">Edit</span> to update the current settings section.</p></div></template>
                                                    <div x-show="!workstationSettingsEditing" class="grid gap-6 xl:grid-cols-2">
                                                        <template x-for="item in workstationReadonlyItems(activeWorkstationSettingsTab)" :key="`${activeWorkstationSettingsTab}-${item.label}`">
                                                            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                                                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-slate-400" x-text="item.label"></p>
                                                                <p class="mt-3 text-[15px] font-bold text-slate-900" x-text="item.value"></p>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <div x-show="workstationSettingsEditing && activeWorkstationSettingsTab === 'application'" class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                                        <div class="mb-5 grid gap-5 md:grid-cols-2">
                                                            <label class="space-y-2">
                                                                <span class="text-sm font-medium text-slate-700">Workstation Name</span>
                                                                <input x-model="workstationSettingsForm.application.name" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                                            </label>
                                                            <label class="space-y-2">
                                                                <span class="text-sm font-medium text-slate-700">Workgroup</span>
                                                                <div class="relative" @click.outside="closeInlineSelect()">
                                                                    <button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400" :disabled="!workstationDetail?.permissions?.changeWorkgroup" @click="workstationDetail?.permissions?.changeWorkgroup && toggleInlineSelect('workgroup_id')">
                                                                        <span class="truncate" x-text="dropdownDisplayLabel('workgroup_id', workstationSettingsForm.application.workgroup_id, null, 'Select workgroup')"></span>
                                                                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                                    </button>
                                                                    <div x-show="isInlineSelectOpen('workgroup_id')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                                        <input x-ref="search-workgroup_id" x-model="workstationOptionSearch.workgroup_id" type="text" placeholder="Search workgroups..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                                        <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('workgroup_id')"></p>
                                                                        <div class="max-h-56 space-y-1 overflow-y-auto">
                                                                            <template x-for="option in filteredWorkstationOptionList('workgroup_id')" :key="`workgroup-opt-${option.value}`">
                                                                                <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.application.workgroup_id = option.value; closeInlineSelect()" x-text="option.label"></button>
                                                                            </template>
                                                                            <template x-if="!filteredWorkstationOptionList('workgroup_id').length">
                                                                                <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                                            </template>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                        <div class="grid gap-5 md:grid-cols-2">
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Units of Length</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('units')"><span class="truncate" x-text="dropdownDisplayLabel('units', workstationSettingsForm.application.units, null, 'Select unit')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('units')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-units" x-model="workstationOptionSearch.units" type="text" placeholder="Search units..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('units')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('units')" :key="`units-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.application.units = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('units').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Units of Luminance</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('LumUnits')"><span class="truncate" x-text="dropdownDisplayLabel('LumUnits', workstationSettingsForm.application.LumUnits, null, 'Select luminance unit')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('LumUnits')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-LumUnits" x-model="workstationOptionSearch.LumUnits" type="text" placeholder="Search luminance units..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('LumUnits')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('LumUnits')" :key="`lum-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.application.LumUnits = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('LumUnits').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Veiling Luminance</span><input x-model="workstationSettingsForm.application.AmbientLight" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Ambient Conditions Stable</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('AmbientStable')"><span class="truncate" x-text="dropdownDisplayLabel('AmbientStable', workstationSettingsForm.application.AmbientStable, null, 'Select value')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('AmbientStable')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-AmbientStable" x-model="workstationOptionSearch.AmbientStable" type="text" placeholder="Search values..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('AmbientStable')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('AmbientStable')" :key="`ambient-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.application.AmbientStable = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('AmbientStable').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                        </div>
                                                        <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                                            <label class="flex items-center gap-3 text-sm font-medium text-slate-700"><input x-model="workstationSettingsForm.application.PutDisplaysToEnergySaveMode" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500">Enable Display Energy Save Mode</label>
                                                            <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="workstationSettingsForm.application.PutDisplaysToEnergySaveMode">
                                                                <label class="space-y-2"><span class="text-sm text-slate-600">Start</span><input x-model="workstationSettingsForm.application.StartEnergySaveMode" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                                <label class="space-y-2"><span class="text-sm text-slate-600">End</span><input x-model="workstationSettingsForm.application.EndEnergySaveMode" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div x-show="workstationSettingsEditing && activeWorkstationSettingsTab === 'calibration'" class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                                        <div class="grid gap-5 md:grid-cols-2">
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Preset</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('CalibrationPresents')"><span class="truncate" x-text="dropdownDisplayLabel('CalibrationPresents', workstationSettingsForm.calibration.CalibrationPresents, null, 'Select preset')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('CalibrationPresents')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-CalibrationPresents" x-model="workstationOptionSearch.CalibrationPresents" type="text" placeholder="Search presets..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('CalibrationPresents')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('CalibrationPresents')" :key="`preset-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.calibration.CalibrationPresents = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('CalibrationPresents').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Luminance Response</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('CalibrationType')"><span class="truncate" x-text="dropdownDisplayLabel('CalibrationType', workstationSettingsForm.calibration.CalibrationType, null, 'Select response')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('CalibrationType')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-CalibrationType" x-model="workstationOptionSearch.CalibrationType" type="text" placeholder="Search responses..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('CalibrationType')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('CalibrationType')" :key="`ctype-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.calibration.CalibrationType = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('CalibrationType').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2" x-show="workstationSettingsForm.calibration.ColorTemperatureAdjustment === '20'"><span class="text-sm font-medium text-slate-700">Custom Color Temperature</span><input x-model="workstationSettingsForm.calibration.ColorTemperatureAdjustment_ext" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Max Luminance</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('WhiteLevel_u_extcombo')"><span class="truncate" x-text="dropdownDisplayLabel('WhiteLevel_u_extcombo', workstationSettingsForm.calibration.WhiteLevel_u_extcombo, null, 'Select luminance')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('WhiteLevel_u_extcombo')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-WhiteLevel_u_extcombo" x-model="workstationOptionSearch.WhiteLevel_u_extcombo" type="text" placeholder="Search luminance..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('WhiteLevel_u_extcombo')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('WhiteLevel_u_extcombo')" :key="`white-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.calibration.WhiteLevel_u_extcombo = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('WhiteLevel_u_extcombo').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2" x-show="workstationSettingsForm.calibration.WhiteLevel_u_extcombo === 'custom'"><span class="text-sm font-medium text-slate-700">Custom Max Luminance</span><input x-model="workstationSettingsForm.calibration.WhiteLevel_u_input" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Gamut</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('gamut_name')"><span class="truncate" x-text="dropdownDisplayLabel('gamut_name', workstationSettingsForm.calibration.gamut_name, null, 'Select gamut')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('gamut_name')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-gamut_name" x-model="workstationOptionSearch.gamut_name" type="text" placeholder="Search gamuts..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('gamut_name')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('gamut_name')" :key="`gamut-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.calibration.gamut_name = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('gamut_name').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                        </div>
                                                        <label class="mt-5 flex items-center gap-3 text-sm font-medium text-slate-700"><input x-model="workstationSettingsForm.calibration.CreateICCICMProfile" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500">Create Display ICC Profile</label>
                                                    </div>
                                                    <div x-show="workstationSettingsEditing && activeWorkstationSettingsTab === 'qa'" class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                                        <div class="grid gap-5 md:grid-cols-2">
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Regulation</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('UsedRegulation')"><span class="truncate" x-text="dropdownDisplayLabel('UsedRegulation', workstationSettingsForm.qa.UsedRegulation, null, 'Select regulation')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('UsedRegulation')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-UsedRegulation" x-model="workstationOptionSearch.UsedRegulation" type="text" placeholder="Search regulations..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('UsedRegulation')"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('UsedRegulation')" :key="`reg-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.qa.UsedRegulation = option.value; closeInlineSelect(); refreshWorkstationClassificationOptions() " x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('UsedRegulation').length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Display Category</span><div class="relative" @click.outside="closeInlineSelect()"><button type="button" class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 transition" @click="toggleInlineSelect('UsedClassification')"><span class="truncate" x-text="dropdownDisplayLabel('UsedClassification', workstationSettingsForm.qa.UsedClassification, workstationQaClassificationOptions, 'Select category')"></span><i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i></button><div x-show="isInlineSelectOpen('UsedClassification')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]"><input x-ref="search-UsedClassification" x-model="workstationOptionSearch.UsedClassification" type="text" placeholder="Search categories..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"><p class="mb-2 text-[11px] font-medium text-slate-400" x-text="workstationOptionHint('UsedClassification', workstationQaClassificationOptions)"></p><div class="max-h-56 space-y-1 overflow-y-auto"><template x-for="option in filteredWorkstationOptionList('UsedClassification', workstationQaClassificationOptions)" :key="`class-opt-${option.value}`"><button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="workstationSettingsForm.qa.UsedClassification = option.value; closeInlineSelect()" x-text="option.label"></button></template><template x-if="!filteredWorkstationOptionList('UsedClassification', workstationQaClassificationOptions).length"><div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div></template></div></div></div></label>
                                                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Body Region</span><input x-model="workstationSettingsForm.qa.bodyRegion" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                        </div>
                                                        <label class="mt-5 flex items-center gap-3 text-sm font-medium text-slate-700"><input x-model="workstationSettingsForm.qa.AutoDailyTests" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500">Start daily tests automatically</label>
                                                    </div>
                                                    <div x-show="workstationSettingsEditing && activeWorkstationSettingsTab === 'location'" class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-sm">
                                                        <div class="grid gap-5 md:grid-cols-2">
                                                            <template x-for="field in workstationLocationFields" :key="field.key">
                                                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700" x-text="field.label"></span><input x-model="workstationSettingsForm.location[field.key]" type="text" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></label>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ============================== -->
            <!-- 4. DISPLAY VIEW (Settings) -->
            <!-- ============================== -->
            <template x-if="current.type === 'display'">
                <div class="flex flex-col h-full w-full">
                    <!-- Header -->
                    <div class="relative bg-gradient-to-r from-[#1175FF] to-[#0A62F0] px-8 py-6 shrink-0 overflow-hidden border-b border-[#0A62F0]/50">
                        <!-- Dot Pattern -->
                        <div class="absolute inset-0 z-0 opacity-[0.25] pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 1) 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
                        
                        <div class="flex items-center gap-5 relative z-10 text-white">
                            <div class="w-[3.5rem] h-[3.5rem] rounded-[1rem] bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-sm shrink-0 shadow-inner">
                                <i data-lucide="monitor" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-extrabold uppercase tracking-widest opacity-90 mb-0.5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1)">{{ __('Display Settings Configuration') }}</p>
                                <h2 class="text-[32px] leading-tight font-extrabold tracking-tight drop-shadow-sm truncate" x-text="displayDetail?.name || @js(__('Loading display…'))">Dell U3219Q (9N6WXV2)</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="relative flex-1 flex overflow-hidden w-full no-scrollbar">
                        <div class="absolute inset-0 z-20 bg-white">
                            <template x-if="displayLoading">
                                <div class="flex h-full items-center justify-center">
                                    <div class="flex flex-col items-center gap-4 text-slate-500">
                                        <div class="h-12 w-12 rounded-full border-4 border-sky-200 border-t-sky-500 animate-spin"></div>
                                        <p class="text-sm font-semibold">{{ __('Loading display details...') }}</p>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!displayLoading && displayError">
                                <div class="flex h-full items-center justify-center p-10">
                                    <div class="max-w-md rounded-[1.5rem] border border-rose-200 bg-rose-50 p-8 text-center">
                                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-rose-500">{{ __('Unable To Load') }}</p>
                                        <p class="mt-3 text-sm text-rose-700" x-text="displayError"></p>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!displayLoading && !displayError && displayDetail">
                                <div class="flex h-full overflow-hidden">
                                    <div class="w-[300px] border-r border-gray-100 bg-white p-6 shrink-0 overflow-y-auto no-scrollbar flex flex-col gap-6">
                                        <div>
                                            <div class="mb-4 flex items-center justify-between gap-3">
                                                <h4 class="text-[13px] font-extrabold text-gray-900">{{ __('Display Hierarchy') }}</h4>
                                                <button type="button" x-show="displayDetail?.permissions?.move" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700" @click="openDisplayMovePanel()">
                                                    <i data-lucide="arrow-right-left" class="h-3.5 w-3.5"></i>
                                                    {{ __('Move') }}
                                                </button>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <p class="text-[10px] text-gray-500 mb-1 ml-1 font-semibold">{{ __('Facility:') }}</p>
                                                    <button type="button" class="w-full cursor-pointer rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-left text-[12px] font-medium text-gray-700 transition-colors hover:border-sky-200 hover:bg-sky-50/50" title="{{ __('Open facility detail') }}" @click="pushView('facility', displayDetail.hierarchy.facility.id)" x-text="displayDetail.hierarchy.facility.name"></button>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-500 mb-1 ml-1 font-semibold">{{ __('Workgroup:') }}</p>
                                                    <button type="button" class="w-full cursor-pointer rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-left text-[12px] font-medium text-gray-700 transition-colors hover:border-sky-200 hover:bg-sky-50/50" title="{{ __('Open workgroup detail') }}" @click="pushView('workgroup', displayDetail.hierarchy.workgroup.id)" x-text="displayDetail.hierarchy.workgroup.name"></button>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-500 mb-1 ml-1 font-semibold">{{ __('Workstation:') }}</p>
                                                    <button type="button" class="w-full cursor-pointer rounded-lg border border-gray-100 bg-gray-50 px-4 py-2.5 text-left text-[12px] font-medium text-gray-700 transition-colors hover:border-sky-200 hover:bg-sky-50/50" title="{{ __('Open workstation detail') }}" @click="pushView('workstation', displayDetail.hierarchy.workstation.id)" x-text="displayDetail.hierarchy.workstation.name"></button>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-500 mb-1 ml-1 font-semibold">{{ __('Display:') }}</p>
                                                    <div class="px-4 py-2.5 bg-emerald-50/50 border border-emerald-300 rounded-lg text-[12px] font-bold text-emerald-700" x-text="displayDetail.name"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="showDisplayMovePanel" x-cloak class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                            <div class="mb-4 flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">{{ __('Move Display') }}</p>
                                                    <p class="mt-1 text-[11px] leading-relaxed text-slate-500">{{ __('Relocate this display by choosing a new facility, workgroup, and workstation.') }}</p>
                                                </div>
                                                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:text-slate-900" @click="closeDisplayMovePanel()">
                                                    <i data-lucide="x" class="h-4 w-4"></i>
                                                </button>
                                            </div>

                                            <div class="space-y-3">
                                                <div>
                                                    <label class="mb-1.5 block text-[10px] font-semibold text-slate-500">{{ __('Facility') }}</label>
                                                    <div class="relative" @click.outside="closeInlineSelect()">
                                                        <button type="button" class="flex h-10 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" @click="toggleInlineSelect('move-facilities')">
                                                            <span class="truncate" x-text="selectedMoveFacilityName() || @js(__('Select facility'))"></span>
                                                            <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                        </button>
                                                        <div x-show="isInlineSelectOpen('move-facilities')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                            <input x-ref="search-move-facilities" x-model="moveOptionSearch.facilities" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                                            <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="moveOptionHint('facilities')"></p>
                                                            <div class="max-h-56 space-y-1 overflow-y-auto">
                                                                <template x-for="facility in filteredMoveOptions('facilities')" :key="`move-facility-${facility.id}`">
                                                                    <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-[12px] font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="moveForm.facilityId = String(facility.id); closeInlineSelect(); changeMoveFacility()">
                                                                        <span class="truncate" x-text="facility.name"></span>
                                                                    </button>
                                                                </template>
                                                                <template x-if="!filteredMoveOptions('facilities').length">
                                                                    <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="mb-1.5 block text-[10px] font-semibold text-slate-500">{{ __('Workgroup') }}</label>
                                                    <div class="relative" @click.outside="closeInlineSelect()">
                                                        <button type="button" :disabled="!moveForm.facilityId || moveLoading" class="flex h-10 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400" @click="toggleInlineSelect('move-workgroups')">
                                                            <span class="truncate" x-text="selectedMoveWorkgroupName() || @js(__('Select workgroup'))"></span>
                                                            <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                        </button>
                                                        <div x-show="isInlineSelectOpen('move-workgroups')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                            <input x-ref="search-move-workgroups" x-model="moveOptionSearch.workgroups" :disabled="!moveForm.facilityId || moveLoading" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400">
                                                            <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="moveOptionHint('workgroups')"></p>
                                                            <div class="max-h-56 space-y-1 overflow-y-auto">
                                                                <template x-for="workgroup in filteredMoveOptions('workgroups')" :key="`move-workgroup-${workgroup.id}`">
                                                                    <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-[12px] font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="moveForm.workgroupId = String(workgroup.id); closeInlineSelect(); changeMoveWorkgroup()">
                                                                        <span class="truncate" x-text="workgroup.name"></span>
                                                                    </button>
                                                                </template>
                                                                <template x-if="!filteredMoveOptions('workgroups').length">
                                                                    <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="mb-1.5 block text-[10px] font-semibold text-slate-500">{{ __('Workstation') }}</label>
                                                    <div class="relative" @click.outside="closeInlineSelect()">
                                                        <button type="button" :disabled="!moveForm.workgroupId || moveLoading" class="flex h-10 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400" @click="toggleInlineSelect('move-workstations')">
                                                            <span class="truncate" x-text="selectedMoveWorkstationName() || @js(__('Select workstation'))"></span>
                                                            <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                                                        </button>
                                                        <div x-show="isInlineSelectOpen('move-workstations')" x-cloak class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                                                            <input x-ref="search-move-workstations" x-model="moveOptionSearch.workstations" :disabled="!moveForm.workgroupId || moveLoading" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-[12px] font-medium text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:bg-slate-100 disabled:text-slate-400">
                                                            <p class="mb-2 text-[11px] font-medium text-slate-400" x-text="moveOptionHint('workstations')"></p>
                                                            <div class="max-h-56 space-y-1 overflow-y-auto">
                                                                <template x-for="workstation in filteredMoveOptions('workstations')" :key="`move-workstation-${workstation.id}`">
                                                                    <button type="button" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-[12px] font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700" @click="moveForm.workstationId = String(workstation.id); closeInlineSelect()">
                                                                        <span class="truncate" x-text="workstation.name"></span>
                                                                    </button>
                                                                </template>
                                                                <template x-if="!filteredMoveOptions('workstations').length">
                                                                    <div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">{{ __('No options found') }}</div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex items-center justify-end gap-2">
                                                <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-[12px] font-semibold text-slate-600 transition hover:bg-slate-100" @click="closeDisplayMovePanel()">{{ __('Cancel') }}</button>
                                                <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-3 py-2 text-[12px] font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="moveLoading || !moveForm.workstationId" @click="confirmDisplayMove()">
                                                    <i data-lucide="arrow-right-left" class="h-3.5 w-3.5"></i>
                                                    <span x-text="moveLoading ? @js(__('Moving...')) : @js(__('Move Display'))"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50/80 rounded-2xl p-5 border border-gray-100 mt-2">
                                            <p class="text-[10px] text-gray-500 mb-2 font-semibold">{{ __('Status') }}</p>
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-2.5 h-2.5 rounded-full" :class="displayDetail.statusTone === 'success' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                                <span class="text-sm font-extrabold text-gray-900" x-text="displayDetail.statusLabel"></span>
                                            </div>
                                            <div class="space-y-2 text-[11px] text-gray-500">
                                                <p><span class="font-semibold text-gray-700">{{ __('Connection:') }}</span> <span x-text="displayDetail.connectedLabel"></span></p>
                                                <p><span class="font-semibold text-gray-700">{{ __('Last sync:') }}</span> <span x-text="displayDetail.lastSync"></span></p>
                                            </div>
                                            <p class="mt-4 rounded-xl border border-rose-100 bg-white px-3 py-2 text-[11px] leading-relaxed text-gray-600" x-text="displayDetail.latestError"></p>
                                        </div>

                                        <div class="pt-6 border-t border-gray-100 border-dashed">
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-4">Device Details</p>
                                            <div class="space-y-4">
                                                <div class="flex gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0"><i data-lucide="monitor" class="w-4 h-4"></i></div>
                                                    <div><p class="text-[10px] font-medium text-gray-400">Model</p><p class="text-[12px] font-bold text-gray-900" x-text="displayDetail.model"></p></div>
                                                </div>
                                                <div class="flex gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0"><i data-lucide="hash" class="w-4 h-4"></i></div>
                                                    <div><p class="text-[10px] font-medium text-gray-400">Serial Number</p><p class="text-[12px] font-bold text-gray-900" x-text="displayDetail.serial"></p></div>
                                                </div>
                                                <div class="flex gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0"><i data-lucide="scan-line" class="w-4 h-4"></i></div>
                                                    <div><p class="text-[10px] font-medium text-gray-400">Resolution</p><p class="text-[12px] font-bold text-gray-900" x-text="displayDetail.resolution"></p></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-1 overflow-y-auto bg-white px-8 pt-0 pb-6 no-scrollbar">
                                        <div class="sticky top-0 z-30 -mx-8 mb-8 flex items-start justify-between gap-6 border-b border-gray-100 bg-white px-8 pt-6 backdrop-blur supports-[backdrop-filter]:bg-white/95">
                                            <div class="flex items-center gap-6">
                                                <button type="button" class="pb-3 border-b-2 text-[13px] font-bold transition" :class="activeDisplayTab === 'overview' ? 'border-sky-500 text-sky-600' : 'border-transparent text-gray-500 hover:text-gray-900'" @click="activeDisplayTab = 'overview'">Overview</button>
                                                <button type="button" class="pb-3 border-b-2 text-[13px] font-bold transition" :class="activeDisplayTab === 'history' ? 'border-sky-500 text-sky-600' : 'border-transparent text-gray-500 hover:text-gray-900'" @click="activeDisplayTab = 'history'">History</button>
                                                <button type="button" class="pb-3 border-b-2 text-[13px] font-bold transition" :class="activeDisplayTab === 'settings' ? 'border-sky-500 text-sky-600' : 'border-transparent text-gray-500 hover:text-gray-900'" @click="activeDisplayTab = 'settings'">Settings</button>
                                            </div>
                                            <div x-show="activeDisplayTab === 'settings'" x-cloak class="pb-3">
                                                <template x-if="!isEditingDisplaySettings && displayDetail?.permissions?.edit">
                                                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-sky-600" @click="isEditingDisplaySettings = true; $nextTick(() => lucide.createIcons())">
                                                        <i data-lucide="pen-line" class="h-4 w-4"></i>
                                                        Edit
                                                    </button>
                                                </template>
                                                <template x-if="isEditingDisplaySettings">
                                                    <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50" @click="cancelDisplayEditing()">
                                                        <i data-lucide="x" class="h-4 w-4"></i>
                                                        {{ __('Cancel') }}
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        <div x-show="activeDisplayTab === 'overview'" x-cloak class="space-y-8 pb-10">
                                            <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                <div class="flex items-center justify-between mb-5">
                                                    <div>
                                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Performance Trend') }}</p>
                                                        <h4 class="mt-2 text-lg font-extrabold text-gray-900">{{ __('Recent calibration history') }}</h4>
                                                        <p x-show="historyLoading" x-cloak class="mt-1 text-xs font-medium text-sky-600">{{ __('Updating history...') }}</p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === '30d' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('30d')">30D</button>
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === '90d' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('90d')">90D</button>
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === '180d' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('180d')">180D</button>
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === 'all' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('all')">ALL</button>
                                                    </div>
                                                </div>
                                                <template x-if="displayDetail.history.timeline.length">
                                                    <div class="space-y-4">
                                                        <div class="rounded-2xl border border-gray-100 bg-slate-50 p-4">
                                                            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                                                <div>
                                                                    <p class="text-xs font-semibold text-slate-700" x-text="displayDetail.history.timelineTitle"></p>
                                                                    <p class="mt-1 text-[11px] text-slate-500">{{ __('Stacked bars show run volume by outcome. Blue line shows pass rate.') }}</p>
                                                                </div>
                                                                <p class="text-[11px] font-semibold text-slate-500">
                                                                    {{ __('Bucket:') }}
                                                                    <span class="text-slate-700" x-text="displayDetail.history.bucket"></span>
                                                                </p>
                                                            </div>
                                                            <div class="relative">
                                                                <div x-html="performanceTrendSvg(displayDetail.history.timeline)"></div>
                                                                <div class="absolute inset-x-0 top-0 z-10 h-[calc(100%-1.25rem)]">
                                                                    <template x-for="(bucket, index) in displayDetail.history.timeline" :key="`trend-hit-${bucket.key}`">
                                                                        <button type="button"
                                                                                class="absolute top-0 h-full cursor-pointer bg-transparent"
                                                                                :style="performanceTrendBucketStyle(index, displayDetail.history.timeline)"
                                                                                @mouseenter="showPerformanceTrendTooltip(bucket, $event)"
                                                                                @mousemove="movePerformanceTrendTooltip(bucket, $event)"
                                                                                @mouseleave="hidePerformanceTrendTooltip()"
                                                                                @click="togglePerformanceTrendBucket(bucket)">
                                                                            <span class="sr-only" x-text="bucket.label"></span>
                                                                        </button>
                                                                    </template>
                                                                </div>
                                                                <div class="mt-3 grid gap-2 text-center" :style="performanceTrendLabelGridStyle(displayDetail.history.timeline)">
                                                                    <template x-for="(bucket, index) in displayDetail.history.timeline" :key="`trend-label-${bucket.key}`">
                                                                        <div>
                                                                            <template x-if="shouldRenderPerformanceTrendLabel(index, displayDetail.history.timeline)">
                                                                                <p class="text-[11px] font-semibold tracking-[0.02em] text-slate-400" x-text="bucket.label"></p>
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <div x-show="performanceTrendTooltip.visible"
                                                                     x-cloak
                                                                     class="pointer-events-none absolute z-20 w-64 rounded-2xl border border-slate-200 bg-white/98 p-4 shadow-[0_18px_50px_rgba(15,23,42,0.2)] backdrop-blur-sm"
                                                                     :style="`left:${performanceTrendTooltip.x}px; top:${performanceTrendTooltip.y}px;`">
                                                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400" x-text="performanceTrendTooltip.bucket?.rangeLabel || '-'"></p>
                                                                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                                            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Total Runs') }}</p>
                                                                            <p class="mt-1 text-lg font-extrabold text-slate-900" x-text="performanceTrendTooltip.bucket?.total ?? 0"></p>
                                                                        </div>
                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5">
                                                                            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Pass Rate') }}</p>
                                                                            <p class="mt-1 text-lg font-extrabold text-sky-600"><span x-text="performanceTrendTooltip.bucket?.passRate ?? 0"></span>%</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-3 space-y-2 text-sm">
                                                                        <div class="flex items-center justify-between gap-3 text-slate-600"><span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span>{{ __('Passed') }}</span><span class="font-bold text-slate-900" x-text="performanceTrendTooltip.bucket?.passed ?? 0"></span></div>
                                                                        <div class="flex items-center justify-between gap-3 text-slate-600"><span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-rose-500"></span>{{ __('Failed') }}</span><span class="font-bold text-slate-900" x-text="performanceTrendTooltip.bucket?.failed ?? 0"></span></div>
                                                                        <div class="flex items-center justify-between gap-3 text-slate-600"><span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-amber-500"></span>{{ __('Skipped / Cancelled') }}</span><span class="font-bold text-slate-900" x-text="performanceTrendTooltip.bucket?.other ?? 0"></span></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-wrap gap-3 text-[11px] font-semibold text-gray-500">
                                                            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span> {{ __('Passed') }}</span>
                                                            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-rose-500"></span> {{ __('Failed') }}</span>
                                                            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-sm bg-amber-500"></span> {{ __('Skipped / Cancelled') }}</span>
                                                            <span class="inline-flex items-center gap-2"><span class="h-0.5 w-4 rounded-full bg-sky-500"></span> {{ __('Pass Rate') }}</span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="!displayDetail.history.timeline.length">
                                                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">{{ __('No history data available yet.') }}</div>
                                                </template>
                                            </div>

                                            <div class="grid gap-4 lg:grid-cols-2">
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-5 shadow-sm">
                                                    <div class="flex items-start justify-between gap-6">
                                                        <div>
                                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Pass Rate') }}</p>
                                                            <p class="mt-2 text-sm font-medium text-gray-500">{{ __('Current success ratio in selected period') }}</p>
                                                        </div>
                                                        <p class="text-4xl font-extrabold text-gray-900"><span x-text="displayDetail.history.passRate"></span>%</p>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-5 shadow-sm">
                                                    <div class="flex items-start justify-between gap-6">
                                                        <div>
                                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Total Histories') }}</p>
                                                            <p class="mt-2 text-sm font-medium text-gray-500">{{ __('Recorded runs included in this view') }}</p>
                                                        </div>
                                                        <p class="text-4xl font-extrabold text-gray-900" x-text="displayDetail.history.total"></p>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-5 shadow-sm">
                                                    <div class="flex items-start justify-between gap-6">
                                                        <div>
                                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Passed') }}</p>
                                                            <p class="mt-2 text-sm font-medium text-gray-500">{{ __('Runs completed successfully') }}</p>
                                                        </div>
                                                        <p class="text-4xl font-extrabold text-emerald-600" x-text="displayDetail.history.passed"></p>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-5 shadow-sm">
                                                    <div class="flex items-start justify-between gap-6">
                                                        <div>
                                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Failed') }}</p>
                                                            <p class="mt-2 text-sm font-medium text-gray-500">{{ __('Runs needing follow-up attention') }}</p>
                                                        </div>
                                                        <p class="text-4xl font-extrabold text-rose-600" x-text="displayDetail.history.failed"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Technical Summary') }}</p>
                                                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Manufacturer') }}</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.manufacturer"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Model') }}</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.model"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Serial') }}</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.serial"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Inventory') }}</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.inventoryNumber"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Type') }}</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.typeOfDisplay"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ __('Technology') }}</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.displayTechnology"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Screen Size</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.screenSize"></p>
                                                    </div>
                                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Installed</p>
                                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.installationDate"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="activeDisplayTab === 'settings' && !isEditingDisplaySettings" x-cloak class="space-y-6 pb-10">
                                            <div class="space-y-6">
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                    <h5 class="text-[11px] font-bold uppercase tracking-widest text-gray-600 mb-6">Calibration Options</h5>
                                                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Exclude</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.exclude ? 'Yes' : 'No'"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Graphicboard LUTs</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.graphicboardOnly ? 'Enabled' : 'Disabled'"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Internal Sensor</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.internalSensor ? 'Enabled' : 'Disabled'"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Current LUT</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.currentLut"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 md:col-span-2 xl:col-span-4">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Installation Date</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.installationDate"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                    <h5 class="text-[11px] font-bold uppercase tracking-widest text-gray-600 mb-6">Technical Settings</h5>
                                                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Manufacturer</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.manufacturer"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Model</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.model"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Serial</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.serial"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Inventory</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.inventoryNumber"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Display Type</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.typeOfDisplay"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Technology</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.displayTechnology"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Screen Size</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.screenSize"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Resolution</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.resolution"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                    <h5 class="text-[11px] font-bold uppercase tracking-widest text-gray-600 mb-6">Financial Settings</h5>
                                                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Purchase Date</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.purchaseDate"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Expected Replacement</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.expectedReplacementDate"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Initial Value</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.initialValue"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Expected Value</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.expectedValue"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Annual Straight Line</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.annualStraightLine"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Monthly Straight Line</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.monthlyStraightLine"></p>
                                                        </div>
                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Current Value</p>
                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="displayDetail.currentValue"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="activeDisplayTab === 'settings' && isEditingDisplaySettings" x-cloak class="space-y-6 pb-10">
                                            <div class="space-y-6">
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                    <h5 class="text-[11px] font-bold uppercase tracking-widest text-gray-600 mb-6">Calibration Options</h5>
                                                    <div class="space-y-5">
                                                        <label class="flex items-center justify-between gap-4">
                                                            <span class="text-sm text-gray-600">Exclude From Calibration</span>
                                                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" x-model="displayForm.exclude">
                                                        </label>
                                                        <label class="flex items-center justify-between gap-4">
                                                            <span class="text-sm text-gray-600">Graphicboard LUTs Only</span>
                                                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" x-model="displayForm.graphicboardOnly">
                                                        </label>
                                                        <label class="flex items-center justify-between gap-4">
                                                            <span class="text-sm text-gray-600">Internal Sensor</span>
                                                            <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" x-model="displayForm.internalSensor">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current LUT Index</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.currentLut">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Installation Date</span>
                                                            <input type="date" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.installationDate">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                    <h5 class="text-[11px] font-bold uppercase tracking-widest text-gray-600 mb-6">Technical Settings</h5>
                                                    <div class="space-y-4">
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Manufacturer</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.manufacturer">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Model</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.model">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Serial</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.serial">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Inventory Number</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.inventoryNumber">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Type Of Display</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.typeOfDisplay">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Display Technology</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.displayTechnology">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Screen Size</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.screenSize">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Resolution H</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.resolutionHorizontal">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Resolution V</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="displayForm.resolutionVertical">
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                    <h5 class="text-[11px] font-bold uppercase tracking-widest text-gray-600 mb-6">Financial Settings</h5>
                                                    <div class="space-y-4">
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Purchase Date</span>
                                                            <input type="date" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.purchaseDate">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Expected Replacement Date</span>
                                                            <input type="date" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.expectedReplacementDate">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Initial Value</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.initialValue">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Expected Value</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.expectedValue">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Annual Straight Line</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.annualStraightLine">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Monthly Straight Line</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.monthlyStraightLine">
                                                        </label>
                                                        <label class="block">
                                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current Value</span>
                                                            <input type="text" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" x-model="financialForm.currentValue">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingDisplaySettings" @click="confirmSaveDisplaySettings()">
                                                    <i data-lucide="save" class="h-4 w-4"></i>
                        <span x-text="savingDisplaySettings ? @js(__('Saving...')) : @js(__('Save Changes'))"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div x-show="activeDisplayTab === 'history'" x-cloak class="space-y-8 pb-10">
                                            <div class="flex flex-wrap items-center justify-between gap-4 rounded-[1.25rem] border border-gray-200 bg-white px-5 py-4 shadow-sm">
                                                <div>
                                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('History Range') }}</p>
                                                    <p class="mt-1 text-sm font-medium text-slate-500">Select the time period used across trends and recent runs.</p>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === '30d' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('30d')">30D</button>
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === '90d' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('90d')">90D</button>
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === '180d' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('180d')">180D</button>
                                                        <button type="button" class="rounded-full px-3 py-1 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60" :disabled="historyLoading" :class="displayPeriod === 'all' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeDisplayPeriod('all')">ALL</button>
                                                    </div>
                                                    <a :href="displayDetail.links.histories" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                                        <i data-lucide="history" class="h-4 w-4"></i>
                                                        {{ __('All History') }}
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">Latest Scored Report</p>
                                            <h4 class="mt-2 text-lg font-extrabold text-gray-900">Most recent scored calibration report in the active range</h4>
                                            <p class="mt-2 max-w-2xl text-sm text-slate-500">This panel summarizes the latest report that contains scored checks. It is separate from the live device health shown in the left sidebar.</p>
                                                    </div>
                                                    <template x-if="displayDetail.history.latestEvaluation">
                                                        <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50" @click="openHistoryReport(displayDetail.history.latestEvaluation)">
                                                            <i data-lucide="file-text" class="h-4 w-4"></i>
                                                    View Full Report
                                                        </button>
                                                    </template>
                                                </div>

                                                <template x-if="displayDetail.history.latestEvaluation">
                                                    <div class="mt-5 space-y-5">
                                                        <div class="flex flex-wrap items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
                                                            <div>
                                                                <p class="text-sm font-semibold text-slate-900" x-text="displayDetail.history.latestEvaluation.name"></p>
                                                                <p class="mt-1 text-xs text-slate-500" x-text="displayDetail.history.latestEvaluation.performedAt"></p>
                                                            </div>
                                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(displayDetail.history.latestEvaluation.resultTone)" x-text="displayDetail.history.latestEvaluation.resultLabel"></span>
                                                        </div>

                                                        <div class="grid gap-4 md:grid-cols-3">
                                                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Scored Checks</p>
                                                                <p class="mt-2 text-2xl font-extrabold text-slate-900" x-text="displayDetail.history.latestEvaluation.totalScores"></p>
                                                            </div>
                                                            <div class="rounded-xl border border-slate-200 bg-emerald-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-emerald-600">Passed Checks</p>
                                                                <p class="mt-2 text-2xl font-extrabold text-emerald-700" x-text="displayDetail.history.latestEvaluation.okScores"></p>
                                                            </div>
                                                            <div class="rounded-xl border border-slate-200 bg-rose-50 px-4 py-3">
                                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-rose-600">Failed Checks</p>
                                                                <p class="mt-2 text-2xl font-extrabold text-rose-700" x-text="displayDetail.history.latestEvaluation.failedScores"></p>
                                                            </div>
                                                        </div>

                                                        <div class="rounded-2xl border border-slate-200">
                                                            <div class="border-b border-slate-200 px-4 py-3">
                                            <p class="text-sm font-semibold text-slate-900">Failed Check Highlights</p>
                                            <p class="mt-1 text-xs text-slate-500">Checks from the latest scored report that missed their target.</p>
                                                            </div>
                                                            <template x-if="displayDetail.history.latestEvaluation.highlights.length">
                                                                <div class="overflow-hidden">
                                                                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                                                                        <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">
                                                                            <tr>
                                                                                <th class="px-4 py-3">Item</th>
                                                                                <th class="px-4 py-3">Target</th>
                                                                                <th class="px-4 py-3">Result</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-slate-100 bg-white">
                                                                            <template x-for="item in displayDetail.history.latestEvaluation.highlights" :key="`${item.section}-${item.name}`">
                                                                                <tr>
                                                                                    <td class="px-4 py-3">
                                                                                        <p class="font-semibold text-slate-900" x-text="item.name"></p>
                                                                                        <p class="mt-1 text-xs text-slate-400" x-text="item.section"></p>
                                                                                    </td>
                                                                                    <td class="px-4 py-3 text-slate-600" x-text="item.limit"></td>
                                                                                    <td class="px-4 py-3 text-slate-600" x-text="item.measured"></td>
                                                                                </tr>
                                                                            </template>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </template>
                                                            <template x-if="!displayDetail.history.latestEvaluation.highlights.length">
                                                <div class="px-4 py-6 text-sm text-emerald-700">No failed checks were found in the latest scored report.</div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                <template x-if="!displayDetail.history.latestEvaluation">
                                                    <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">No scored evaluation was found in the selected period.</div>
                                                </template>
                                            </div>
                                            <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                <div class="flex items-center justify-between mb-5">
                                                    <div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">Measurement Trends</p><h4 class="mt-2 text-lg font-extrabold text-gray-900">Key calibration metrics over time</h4></div>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs font-semibold text-gray-400">Extracted from Target &amp; Results</span>
                                                    </div>
                                                </div>
                                                <template x-if="displayDetail.history.metrics.length">
                                                    <div class="grid gap-4 xl:grid-cols-2">
                                                        <template x-for="metric in displayDetail.history.metrics" :key="metric.key">
                                                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                                                <div class="flex items-start justify-between gap-4">
                                                                    <div>
                                                                        <p class="text-sm font-semibold text-slate-900" x-text="metric.label"></p>
                                                                        <p class="mt-1 text-xs text-slate-500">
                                                                            Latest:
                                                                            <span class="font-semibold text-slate-700" x-text="formatMetricValue(metric.latest, metric.unit)"></span>
                                                                        </p>
                                                                    </div>
                                                                    <div class="text-right text-[11px] text-slate-500">
                                                                        <p>Min <span class="font-semibold text-slate-700" x-text="formatMetricValue(metric.min, metric.unit)"></span></p>
                                                                        <p class="mt-1">Max <span class="font-semibold text-slate-700" x-text="formatMetricValue(metric.max, metric.unit)"></span></p>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-4">
                                                                    <svg viewBox="0 0 100 40" preserveAspectRatio="none" class="h-24 w-full overflow-visible">
                                                                        <polyline
                                                                            fill="none"
                                                                            stroke="#0ea5e9"
                                                                            stroke-width="1.35"
                                                                            stroke-opacity="0.95"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            :points="sparklinePoints(metric.points)"
                                                                        ></polyline>
                                                                    </svg>
                                                                </div>
                                                                <div class="mt-2 flex flex-wrap justify-between gap-2 text-[11px] text-slate-400">
                                                                    <template x-for="point in metric.points" :key="`${metric.key}-${point.label}-${point.value}`">
                                                                        <span x-text="point.label"></span>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!displayDetail.history.metrics.length">
                                                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">No measurable numeric trends were found for this display in the selected period.</div>
                                                </template>
                                            </div>

                                            <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                <div class="flex items-center justify-between mb-5">
                                                    <div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">Timeline Mix</p><h4 class="mt-2 text-lg font-extrabold text-gray-900">Pass / fail distribution by period</h4></div>
                                                    <span class="text-xs font-semibold text-gray-400">Based on active filter</span>
                                                </div>
                                                <template x-if="displayDetail.history.timeline.length">
                                                    <div class="space-y-4">
                                                        <template x-for="bucket in displayDetail.history.timeline" :key="`timeline-${bucket.label}`">
                                                            <div class="space-y-2">
                                                                <div class="flex items-center justify-between text-xs">
                                                                    <span class="font-semibold text-gray-700" x-text="bucket.label"></span>
                                                                    <span class="text-gray-400" x-text="`${bucket.total} runs`"></span>
                                                                </div>
                                                                <div class="flex h-3 w-full overflow-hidden rounded-full bg-slate-100">
                                                                    <div class="bg-emerald-500" :style="`width:${bucket.passedPct}%`"></div>
                                                                    <div class="bg-rose-500" :style="`width:${bucket.failedPct}%`"></div>
                                                                    <div class="bg-amber-400" :style="`width:${bucket.otherPct}%`"></div>
                                                                </div>
                                                                <div class="flex flex-wrap gap-4 text-[11px] text-gray-500">
                                                                    <span>Passed: <span class="font-semibold text-emerald-600" x-text="bucket.passed"></span></span>
                                                                    <span>Failed: <span class="font-semibold text-rose-600" x-text="bucket.failed"></span></span>
                                                                    <span>Other: <span class="font-semibold text-amber-600" x-text="bucket.other"></span></span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!displayDetail.history.timeline.length">
                                                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">No timeline data available for the selected period.</div>
                                                </template>
                                            </div>

                                            <div class="rounded-[1.25rem] border border-gray-200 bg-white p-6 shadow-sm">
                                                <div class="flex items-center justify-between mb-5">
                                                    <div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gray-400">Recent Runs</p><h4 class="mt-2 text-lg font-extrabold text-gray-900">Calibration and QA history</h4></div>
                                                    <div class="flex items-center gap-3">
                                                        <template x-if="selectedPerformanceTrendBucket">
                                                            <button type="button" class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[11px] font-semibold text-sky-700 transition hover:bg-sky-100" @click="clearPerformanceTrendBucket()">
                                                                <i data-lucide="x" class="h-3.5 w-3.5"></i>
                                                                <span x-text="selectedPerformanceTrendBucket.rangeLabel"></span>
                                                            </button>
                                                        </template>
                                                        <span class="text-xs font-semibold text-gray-400" x-text="`${filteredRecentHistory().length} records`"></span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <template x-for="item in filteredRecentHistory()" :key="`history-${item.id}`">
                                                        <button type="button" class="flex w-full items-center justify-between gap-4 rounded-2xl border border-gray-100 bg-slate-50 px-4 py-3 text-left transition hover:border-sky-200 hover:bg-sky-50/40" @click="openHistoryReport(item)">
                                                            <div>
                                                                <p class="text-sm font-semibold text-gray-900" x-text="item.name"></p>
                                                                <p class="mt-1 text-xs text-gray-500"><span x-text="item.performedAt"></span><span class="mx-1">·</span><span x-text="item.bucketRangeLabel"></span></p>
                                                            </div>
                                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(item.resultTone)" x-text="item.resultLabel"></span>
                                                        </button>
                                                    </template>
                                                    <template x-if="!filteredRecentHistory().length">
                                                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">No history records match the selected bucket.</div>
                                                    </template>
                                                </div>
                                                <div x-show="false && historyReportOpen" x-cloak class="space-y-6">
                                                    <template x-if="historyReportLoading">
                                                        <div class="flex min-h-[220px] items-center justify-center rounded-2xl border border-slate-200 bg-slate-50">
                                                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-5 text-sm font-semibold text-slate-500 shadow-sm">{{ __('Loading history report...') }}</div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!historyReportLoading && historyReportError">
                                                        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-6 py-5 text-sm text-rose-700" x-text="historyReportError"></div>
                                                    </template>
                                                    <template x-if="!historyReportLoading && !historyReportError && historyReportDetail">
                                                        <div class="space-y-6">
                                                            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                                                <div class="flex flex-wrap items-start justify-between gap-4">
                                                                    <div>
                                                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Performed At</p>
                                                                        <h4 class="mt-2 text-xl font-extrabold text-slate-900" x-text="historyReportDetail.performedAt"></h4>
                                                                    </div>
                                                                    <div class="flex items-center gap-3">
                                                                        <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(historyReportDetail.resultTone)" x-text="historyReportDetail.resultLabel"></span>
                                                                        <a x-show="historyReportDetail?.printUrl" :href="historyReportDetail?.printUrl" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                                                            <i data-lucide="printer" class="h-4 w-4"></i>
                                                                            Open Full Report
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3"><p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Facility</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.facility"></p></div>
                                                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3"><p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Workgroup</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.workgroup"></p></div>
                                                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3"><p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Workstation</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.workstation"></p></div>
                                                                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3"><p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Display</p><p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.display"></p></div>
                                                                </div>
                                                            </div>
                                                            <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                                                                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Header Details</p>
                                                                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                                                                    <template x-for="item in historyReportDetail.header" :key="`history-header-${item.label}`">
                                                                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                                                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400" x-text="item.label"></p>
                                                                            <p class="mt-2 text-sm font-bold text-slate-900" x-text="item.value"></p>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <template x-for="section in historyReportDetail.sections" :key="`history-section-${section.name}`">
                                                                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                                                                    <div>
                                                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Step</p>
                                                                        <h4 class="mt-2 text-lg font-extrabold text-slate-900" x-text="section.name"></h4>
                                                                    </div>
                                                                    <x-history-scores x-show="section.scores.length" class="mt-6">
                                                                        <template x-for="score in section.scores" :key="`${section.name}-${score.name}`">
                                                                            <tr><td class="px-4 py-3 font-semibold text-slate-900" x-text="score.name"></td><td class="px-4 py-3 text-slate-600" x-html="score.limit"></td><td class="px-4 py-3 text-slate-600" x-html="score.measured"></td><td class="px-4 py-3"><span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(score.statusTone)" x-text="score.statusLabel"></span></td></tr>
                                                                        </template>
                                                                    </x-history-scores>
                                                                    <x-history-questions x-show="section.questions.length" class="mt-6">
                                                                        <template x-for="question in section.questions" :key="`${section.name}-${question.text}`">
                                                                            <div class="flex items-start justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"><p class="text-sm font-medium text-slate-700" x-html="question.text"></p><span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(question.tone)" x-text="question.answer"></span></div>
                                                                        </template>
                                                                    </x-history-questions>
                                                                    <div x-show="section.comment" class="mt-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4"><p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Comment</p><p class="mt-2 text-sm text-slate-700" x-text="section.comment"></p></div>
                                                                    <x-history-graphs x-show="section.graphs.length" class="mt-6 md:grid-cols-2">
                                                                        <template x-for="graph in section.graphs" :key="`${section.name}-${graph.url}`">
                                                                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50"><div class="border-b border-slate-200 px-4 py-3"><p class="text-sm font-semibold text-slate-900" x-text="graph.name"></p></div><img :src="graph.url" :alt="graph.name" class="h-auto w-full bg-white"></div>
                                                                        </template>
                                                                    </x-history-graphs>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>

    <template x-teleport="body">
    <div x-cloak x-show="historyReportOpen"
         x-transition.opacity.duration.200ms
         class="fixed inset-0 z-[12000] flex items-center justify-center bg-slate-950/45 backdrop-blur-sm p-6"
         style="z-index: 2147483000;"
         @click.self="closeHistoryReport()">
        <div x-cloak x-show="historyReportOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative z-[12010] flex flex-col overflow-hidden rounded-[1.25rem] border border-slate-200 bg-white shadow-[0_30px_90px_-20px_rgba(15,23,42,0.45)]"
             style="z-index: 2147483001; width: min(680px, calc(100vw - 220px)); height: min(620px, calc(100vh - 160px));"
             @click.stop>
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3.5">
                <div>
                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('History Report') }}</p>
                    <h3 class="mt-1 text-lg font-extrabold text-slate-900" x-text="historyReportTitle"></h3>
                </div>
                <div class="flex items-center gap-3">
                    <a x-show="historyReportDetail?.printUrl" :href="historyReportDetail?.printUrl" target="_blank" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="printer" class="h-4 w-4"></i>
                        Open Print Preview
                    </a>
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-slate-300 hover:text-slate-900" @click="closeHistoryReport()">
                    <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>
            <div class="h-full overflow-y-auto bg-slate-50 px-4 py-4">
                <template x-if="historyReportLoading">
                    <div class="flex h-full min-h-[320px] items-center justify-center">
                                                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-5 text-sm font-semibold text-slate-500 shadow-sm">{{ __('Loading history report...') }}</div>
                    </div>
                </template>
                <template x-if="!historyReportLoading && historyReportError">
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-6 py-5 text-sm text-rose-700" x-text="historyReportError"></div>
                </template>
                <template x-if="!historyReportLoading && !historyReportError && historyReportDetail">
                    <div class="space-y-4">
                        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Performed At</p>
                                    <h4 class="mt-2 text-lg font-extrabold text-slate-900" x-text="historyReportDetail.performedAt"></h4>
                                </div>
                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(historyReportDetail.resultTone)" x-text="historyReportDetail.resultLabel"></span>
                            </div>
                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Facility</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.facility"></p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Workgroup</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.workgroup"></p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Workstation</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.workstation"></p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Display</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900" x-text="historyReportDetail.display.display"></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Header Details</p>
                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <template x-for="item in historyReportDetail.header" :key="`history-header-${item.label}`">
                                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400" x-text="item.label"></p>
                                        <p class="mt-2 text-sm font-bold text-slate-900" x-text="item.value"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <template x-for="section in historyReportDetail.sections" :key="`history-section-${section.name}`">
                            <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Step</p>
                                        <h4 class="mt-2 text-base font-extrabold text-slate-900" x-text="section.name"></h4>
                                    </div>
                                </div>

                                <x-history-scores x-show="section.scores.length" class="mt-4">
                                    <template x-for="score in section.scores" :key="`${section.name}-${score.name}`">
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-slate-900" x-text="score.name"></td>
                                            <td class="px-4 py-3 text-slate-600" x-html="score.limit"></td>
                                            <td class="px-4 py-3 text-slate-600" x-html="score.measured"></td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(score.statusTone)" x-text="score.statusLabel"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </x-history-scores>

                                <x-history-questions x-show="section.questions.length" class="mt-4">
                                    <template x-for="question in section.questions" :key="`${section.name}-${question.text}`">
                                        <div class="flex items-start justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                            <p class="text-sm font-medium text-slate-700" x-html="question.text"></p>
                                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold" :class="historyBadgeClass(question.tone)" x-text="question.answer"></span>
                                        </div>
                                    </template>
                                </x-history-questions>

                                <div x-show="section.comment" class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Comment</p>
                                    <p class="mt-2 text-sm text-slate-700" x-text="section.comment"></p>
                                </div>

                                <x-history-graphs x-show="section.graphs.length" class="mt-4">
                                    <template x-for="graph in section.graphs" :key="`${section.name}-${graph.name}`">
                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                            <div class="border-b border-slate-200 px-4 py-3">
                                                <p class="text-sm font-semibold text-slate-900" x-text="graph.name"></p>
                                            </div>
                                            <template x-if="graph.chart">
                                                <div class="bg-white p-4">
                                                    <div x-html="historyGraphSvg(graph)"></div>
                                                </div>
                                            </template>
                                            <template x-if="!graph.chart && graph.url">
                                                <img :src="graph.url" :alt="graph.name" class="h-auto w-full bg-white">
                                            </template>
                                        </div>
                                    </template>
                                </x-history-graphs>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
        <div x-show="workgroupSettingsConfirmOpen"
             x-cloak
             class="fixed inset-0 flex items-center justify-center bg-slate-950/35 px-4"
             style="z-index: 2147483188;"
             x-transition.opacity
             @click.self="closeWorkgroupSettingsConfirm()">
            <div x-show="workgroupSettingsConfirmOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-2 scale-[0.98] opacity-0"
                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                 x-transition:leave-end="translate-y-2 scale-[0.98] opacity-0"
                 class="w-full max-w-md overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.24)]"
                 style="z-index: 2147483189;">
                <div class="border-b border-slate-100 px-6 py-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Confirm Save</p>
                    <h3 class="mt-2 text-xl font-extrabold tracking-tight text-slate-900">Save Workgroup Settings?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">This will update the workgroup profile shown in this modal.</p>
                </div>
                <div class="space-y-4 px-6 py-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Workgroup</p>
                        <p class="mt-3 text-base font-bold text-slate-900" x-text="workgroupSettingsForm.name || '-'"></p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Facility</p>
                            <p class="mt-3 text-sm font-bold text-slate-900" x-text="selectedWorkgroupFacilityName() || '-'"></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Phone Number</p>
                            <p class="mt-3 text-sm font-bold text-slate-900" x-text="workgroupSettingsForm.phone || '-'"></p>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Address</p>
                        <p class="mt-3 text-sm text-slate-700" x-text="workgroupSettingsForm.address || '-'"></p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" @click="closeWorkgroupSettingsConfirm()">Cancel</button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingWorkgroupSettings" @click="saveWorkgroupSettings()">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        <span x-text="savingWorkgroupSettings ? @js(__('Saving...')) : @js(__('Confirm Save'))"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-cloak x-show="workstationSettingsConfirmOpen"
             x-cloak
             class="fixed inset-0 flex items-center justify-center bg-slate-950/35 px-4"
             style="z-index: 2147483190;"
             x-transition.opacity
             @click.self="closeWorkstationSettingsConfirm()">
            <div x-cloak x-show="workstationSettingsConfirmOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-2 scale-[0.98] opacity-0"
                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                 x-transition:leave-end="translate-y-2 scale-[0.98] opacity-0"
                 class="w-full max-w-md overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.24)]"
                 style="z-index: 2147483191;">
                <div class="border-b border-slate-100 px-6 py-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Confirm Save</p>
                    <h3 class="mt-2 text-xl font-extrabold tracking-tight text-slate-900">Save Workstation Settings?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('Please confirm the changes for the active workstation settings section before saving.') }}</p>
                </div>

                <div class="space-y-4 px-6 py-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Workstation</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Name:</span> <span x-text="workstationDetail?.name || '-'"></span></p>
                            <p><span class="font-semibold text-slate-900">Facility:</span> <span x-text="workstationDetail?.facility?.name || '-'"></span></p>
                            <p><span class="font-semibold text-slate-900">Workgroup:</span> <span x-text="workstationDetail?.workgroup?.name || '-'"></span></p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sky-200 bg-sky-50/70 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-sky-500">Active Section</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Tab:</span> <span x-text="workstationSettingsTabs.find((tab) => tab.key === activeWorkstationSettingsTab)?.label || '-'"></span></p>
                            <p class="text-slate-500">Only the values in the active settings section will be saved.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" @click="closeWorkstationSettingsConfirm()">Cancel</button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingWorkstationSettings" @click="saveWorkstationSettings()">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        <span x-text="savingWorkstationSettings ? @js(__('Saving...')) : @js(__('Confirm Save'))"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-cloak x-show="workstationMoveConfirmOpen"
             x-cloak
             class="fixed inset-0 flex items-center justify-center bg-slate-950/35 px-4"
             style="z-index: 2147483210;"
             x-transition.opacity
             @click.self="closeWorkstationMoveConfirm()">
            <div x-cloak x-show="workstationMoveConfirmOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-2 scale-[0.98] opacity-0"
                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                 x-transition:leave-end="translate-y-2 scale-[0.98] opacity-0"
                 class="w-full max-w-md overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.24)]"
                 style="z-index: 2147483211;">
                <div class="border-b border-slate-100 px-6 py-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Confirm Move</p>
                    <h3 class="mt-2 text-xl font-extrabold tracking-tight text-slate-900">Move This Workstation?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">Please confirm the new hierarchy before this workstation is moved.</p>
                </div>

                <div class="space-y-4 px-6 py-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Current Hierarchy</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Facility:</span> <span x-text="workstationDetail?.facility?.name || '-'"></span></p>
                            <p><span class="font-semibold text-slate-900">Workgroup:</span> <span x-text="workstationDetail?.workgroup?.name || '-'"></span></p>
                            <p><span class="font-semibold text-slate-900">Workstation:</span> <span x-text="workstationDetail?.name || '-'"></span></p>
                        </div>
                    </div>

                    <div class="flex justify-center text-sky-500">
                        <i data-lucide="arrow-down" class="h-5 w-5"></i>
                    </div>

                    <div class="rounded-2xl border border-sky-200 bg-sky-50/70 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-sky-500">New Hierarchy</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Facility:</span> <span x-text="selectedWorkstationFacilityName()"></span></p>
                            <p><span class="font-semibold text-slate-900">Workgroup:</span> <span x-text="selectedWorkstationWorkgroupName()"></span></p>
                            <p><span class="font-semibold text-slate-900">Workstation:</span> <span x-text="workstationDetail?.name || '-'"></span></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" @click="closeWorkstationMoveConfirm()">Cancel</button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="workstationMoveLoading" @click="saveWorkstationHierarchyMove()">
                        <i data-lucide="arrow-right-left" class="h-4 w-4"></i>
                        <span x-text="workstationMoveLoading ? 'Moving...' : 'Confirm Move'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-cloak x-show="moveConfirmOpen"
             x-cloak
             class="fixed inset-0 flex items-center justify-end bg-slate-950/35 px-4"
             style="z-index: 2147483200;"
             x-transition.opacity
             @click.self="closeDisplayMoveConfirm()">
            <div x-cloak x-show="moveConfirmOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-2 scale-[0.98] opacity-0"
                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                 x-transition:leave-end="translate-y-2 scale-[0.98] opacity-0"
                 class="w-full max-w-md overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.24)]"
                 style="z-index: 2147483201; margin-right: max(20px, calc((min(1080px, 100vw - 120px) - min(28rem, 100vw - 2rem)) / 2 - 28px));">
                <div class="border-b border-slate-100 px-6 py-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Confirm Move</p>
                    <h3 class="mt-2 text-xl font-extrabold tracking-tight text-slate-900">Move This Display?</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">Please confirm the new destination before this display is moved.</p>
                </div>

                <div class="space-y-4 px-6 py-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Current Location') }}</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Facility:</span> <span x-text="displayDetail?.hierarchy?.facility?.name || '-'"></span></p>
                            <p><span class="font-semibold text-slate-900">Workgroup:</span> <span x-text="displayDetail?.hierarchy?.workgroup?.name || '-'"></span></p>
                            <p><span class="font-semibold text-slate-900">Workstation:</span> <span x-text="displayDetail?.hierarchy?.workstation?.name || '-'"></span></p>
                        </div>
                    </div>

                    <div class="flex justify-center text-sky-500">
                        <i data-lucide="arrow-down" class="h-5 w-5"></i>
                    </div>

                    <div class="rounded-2xl border border-sky-200 bg-sky-50/70 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-sky-500">{{ __('New Location') }}</p>
                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Facility:</span> <span x-text="selectedMoveFacilityName()"></span></p>
                            <p><span class="font-semibold text-slate-900">Workgroup:</span> <span x-text="selectedMoveWorkgroupName()"></span></p>
                            <p><span class="font-semibold text-slate-900">Workstation:</span> <span x-text="selectedMoveWorkstationName()"></span></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" @click="closeDisplayMoveConfirm()">Cancel</button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="moveLoading" @click="moveDisplayHierarchy()">
                        <i data-lucide="arrow-right-left" class="h-4 w-4"></i>
                        <span x-text="moveLoading ? 'Moving...' : 'Confirm Move'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-cloak x-show="settingsConfirmOpen"
             x-cloak
             class="fixed inset-0 flex items-center justify-end bg-slate-950/35 px-4"
             style="z-index: 2147483150;"
             x-transition.opacity
             @click.self="closeSettingsSaveConfirm()">
            <div x-cloak x-show="settingsConfirmOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-2 scale-[0.98] opacity-0"
                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                 x-transition:leave-end="translate-y-2 scale-[0.98] opacity-0"
                 class="w-full max-w-md overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.24)]"
                 style="z-index: 2147483151; margin-right: max(20px, calc((min(1080px, 100vw - 120px) - min(28rem, 100vw - 2rem)) / 2 - 28px));">
                <div class="border-b border-slate-100 px-6 py-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Confirm Save') }}</p>
                    <h3 class="mt-2 text-xl font-extrabold tracking-tight text-slate-900">{{ __('Save Display Settings?') }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ __('This will update the display settings and financial values shown in this modal.') }}</p>
                </div>

                <div class="space-y-4 px-6 py-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Display') }}</p>
                        <p class="mt-3 text-base font-bold text-slate-900" x-text="displayDetail?.name || '-'"></p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Technical') }}</p>
                            <div class="mt-3 space-y-2 text-sm text-slate-600">
                                <p><span class="font-semibold text-slate-900">{{ __('Manufacturer:') }}</span> <span x-text="displayForm.manufacturer || '-'"></span></p>
                                <p><span class="font-semibold text-slate-900">{{ __('Model:') }}</span> <span x-text="displayForm.model || '-'"></span></p>
                                <p><span class="font-semibold text-slate-900">{{ __('Serial:') }}</span> <span x-text="displayForm.serial || '-'"></span></p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Financial') }}</p>
                            <div class="mt-3 space-y-2 text-sm text-slate-600">
                                <p><span class="font-semibold text-slate-900">{{ __('Purchase Date:') }}</span> <span x-text="financialForm.purchaseDate || '-'"></span></p>
                                <p><span class="font-semibold text-slate-900">{{ __('Current Value:') }}</span> <span x-text="financialForm.currentValue || '-'"></span></p>
                                <p><span class="font-semibold text-slate-900">{{ __('Replacement:') }}</span> <span x-text="financialForm.expectedReplacementDate || '-'"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" @click="closeSettingsSaveConfirm()">{{ __('Cancel') }}</button>
                    <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60" :disabled="savingDisplaySettings" @click="saveDisplayModal()">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        <span x-text="savingDisplaySettings ? @js(__('Saving...')) : @js(__('Confirm Save'))"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div x-cloak x-show="displayStructureMapOpen || workstationStructureMapOpen || workgroupStructureMapOpen"
             x-cloak
             class="fixed inset-0 flex items-center justify-center bg-slate-950/35 p-4"
             style="z-index: 2147483180;"
             x-transition.opacity
             @click.self="closeDisplayStructureMap()">
            <div x-cloak x-show="displayStructureMapOpen || workstationStructureMapOpen || workgroupStructureMapOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="translate-y-2 scale-[0.98] opacity-0"
                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                 x-transition:leave-end="translate-y-2 scale-[0.98] opacity-0"
                 class="flex h-[92vh] w-[92vw] max-w-none flex-col overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-[0_24px_80px_rgba(15,23,42,0.24)]"
                 style="z-index: 2147483181;">
                <div class="border-b border-slate-100 px-6 py-5 shrink-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">{{ __('Structure Map') }}</p>
                            <h3 class="mt-2 text-xl font-extrabold tracking-tight text-slate-900" x-text="structureMapTitle()">{{ __('Display Hierarchy Map') }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500" x-text="structureMapDescription()">{{ __('Facility to workstation path with sibling displays on the selected workstation. Drag to pan, use zoom controls or mouse wheel.') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <template x-if="current.type === 'workstation' || current.type === 'workgroup'">
                                <div class="mr-2 inline-flex items-center rounded-full border border-slate-200 bg-white p-1">
                                    <button type="button" class="rounded-full px-3 py-1.5 text-xs font-semibold transition" :class="structureMapFilter === 'all' ? 'bg-sky-500 text-white' : 'text-slate-600 hover:bg-slate-50'" @click="setStructureMapFilter('all')"><span x-text="`All (${workstationStructureMapStats().all})`"></span></button>
                                    <button type="button" class="rounded-full px-3 py-1.5 text-xs font-semibold transition" :class="structureMapFilter === 'attention' ? 'bg-rose-500 text-white' : 'text-slate-600 hover:bg-slate-50'" @click="setStructureMapFilter('attention')"><span x-text="structureMapAttentionLabel()"></span></button>
                                    <button type="button" class="rounded-full px-3 py-1.5 text-xs font-semibold transition" :class="structureMapFilter === 'healthy' ? 'bg-emerald-500 text-white' : 'text-slate-600 hover:bg-slate-50'" @click="setStructureMapFilter('healthy')"><span x-text="structureMapHealthyLabel()"></span></button>
                                </div>
                            </template>
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50" @click="autoLayoutStructureMap()">
                                <i data-lucide="sparkles" class="h-4 w-4"></i>
                                {{ __('Auto Layout') }}
                            </button>
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50" @click="zoomStructureMapOut()">
                                <i data-lucide="zoom-out" class="h-4 w-4"></i>
                                {{ __('Out') }}
                            </button>
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50" @click="resetStructureMapView()">
                                <i data-lucide="scan-search" class="h-4 w-4"></i>
                                {{ __('Reset') }}
                            </button>
                            <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50" @click="zoomStructureMapIn()">
                                <i data-lucide="zoom-in" class="h-4 w-4"></i>
                                {{ __('In') }}
                            </button>
                            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 transition hover:border-slate-300 hover:text-slate-900" @click="closeDisplayStructureMap()">
                                <i data-lucide="x" class="h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex min-h-0 flex-1 flex-col bg-slate-50 px-6 py-6">
                    <div class="relative flex min-h-0 flex-1 flex-col overflow-hidden rounded-[1.5rem] border border-slate-200 bg-[radial-gradient(circle_at_1px_1px,rgba(148,163,184,0.18)_1px,transparent_0)] bg-[size:24px_24px]">
                        <div class="flex items-center justify-between border-b border-slate-200 bg-white/90 px-5 py-3 backdrop-blur-sm">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                                {{ __('Zoom') }}
                                <span class="ml-2 text-slate-700" x-text="`${Math.round(structureMap.zoom * 100)}%`"></span>
                            </div>
                            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">{{ __('Drag nodes or canvas to inspect the hierarchy') }}</div>
                        </div>
                        <div x-ref="structureMapViewport" class="relative h-full min-h-0 flex-1 overflow-hidden">
                            <div x-ref="structureGraphContainer" class="h-full w-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function hierarchyModal() {
        return {
            isOpen: false,
            viewStack: [],
            facilityLoading: false,
            facilityError: '',
            facilityDetail: null,
            facilityEditing: false,
            savingFacility: false,
            facilityForm: {
                name: '',
                description: '',
                location: '',
                timezone: '',
            },
            displayLoading: false,
            historyLoading: false,
            displayError: '',
            displayDetail: null,
            workgroupLoading: false,
            workgroupError: '',
            workgroupDetail: null,
            activeWorkgroupTab: 'overview',
            workgroupSettingsEditing: false,
            savingWorkgroupSettings: false,
            workgroupSettingsConfirmOpen: false,
            workgroupOptionSearch: {
                facilities: '',
            },
            workgroupSettingsForm: {
                name: '',
                address: '',
                phone: '',
                facility_id: '',
            },
            workstationLoading: false,
            workstationError: '',
            workstationDetail: null,
            activeWorkstationTab: 'overview',
            activeWorkstationSettingsTab: 'application',
            workstationSettingsLoading: false,
            workstationSettingsError: '',
            workstationSettingsReady: false,
            workstationSettingsEditing: false,
            savingWorkstationSettings: false,
            workstationSettingsConfirmOpen: false,
            showWorkstationHierarchyEdit: false,
            workstationMoveLoading: false,
            workstationMoveConfirmOpen: false,
            workstationSettingsTabs: [
                { key: 'application', label: @js(__('Application')) },
                { key: 'calibration', label: @js(__('Display Calibration')) },
                { key: 'qa', label: @js(__('Quality Assurance')) },
                { key: 'location', label: @js(__('Location')) },
            ],
            workstationQaClassificationOptions: [],
            workstationLocationFields: [
                { key: 'Facility', label: @js(__('Facility Label')) },
                { key: 'Department', label: @js(__('Department')) },
                { key: 'Room', label: @js(__('Room')) },
                { key: 'ResponsiblePersonName', label: @js(__('Responsible Person')) },
                { key: 'ResponsiblePersonCity', label: @js(__('Address')) },
                { key: 'ResponsiblePersonAddress', label: @js(__('City')) },
                { key: 'ResponsiblePersonEmail', label: @js(__('Email')) },
                { key: 'ResponsiblePersonPhoneNumber', label: @js(__('Phone Number')) },
            ],
            workstationSettingsOptions: {},
            workstationOptionSearch: {},
            workstationMoveOptions: {
                facilities: [],
                workgroups: [],
            },
            workstationMoveSearch: {
                facilities: '',
                workgroups: '',
            },
            workstationMoveForm: {
                facilityId: '',
                workgroupId: '',
            },
            moveOptionSearch: {
                facilities: '',
                workgroups: '',
                workstations: '',
            },
            activeInlineSelect: null,
            workstationSettingsForm: {
                application: {},
                calibration: {},
                qa: {},
                location: {},
            },
            historyReportOpen: false,
            historyReportLoading: false,
            historyReportError: '',
            historyReportDetail: null,
            historyReportTitle: @js(__('History Report')),
            activeDisplayTab: 'overview',
            displayPeriod: 'all',
            isEditingDisplaySettings: false,
            savingDisplaySettings: false,
            settingsConfirmOpen: false,
            displayStructureMapOpen: false,
            workgroupStructureMapOpen: false,
            workstationStructureMapOpen: false,
            structureGraphInstance: null,
            structureMap: {
                zoom: 1,
                panX: 0,
                panY: 0,
                dragging: false,
                dragStartX: 0,
                dragStartY: 0,
                startPanX: 0,
                startPanY: 0,
            },
            structureMapFilter: 'all',
            structureMapExpandedWorkstationId: null,
            structureMapExpandedWorkgroupId: null,
            structureNodes: {
                facility: { x: 80, y: 96, w: 170, h: 72 },
                workgroup: { x: 360, y: 96, w: 170, h: 72 },
                workstation: { x: 640, y: 96, w: 190, h: 72 },
                splitter: { x: 880, y: 108, w: 130, h: 58 },
                displays: {},
                boardWidth: 1400,
                boardHeight: 520,
            },
            structureNodeDrag: {
                active: false,
                type: null,
                id: null,
                startX: 0,
                startY: 0,
                originX: 0,
                originY: 0,
            },
            showDisplayMovePanel: false,
            moveConfirmOpen: false,
            moveLoading: false,
            performanceTrendTooltip: {
                visible: false,
                x: 0,
                y: 0,
                bucket: null,
            },
            selectedPerformanceTrendBucket: null,
            moveOptions: {
                facilities: [],
                workgroups: [],
                workstations: [],
            },
            moveForm: {
                facilityId: '',
                workgroupId: '',
                workstationId: '',
            },
            displayForm: {
                exclude: false,
                graphicboardOnly: false,
                internalSensor: false,
                currentLut: '',
                installationDate: '',
                manufacturer: '',
                model: '',
                serial: '',
                inventoryNumber: '',
                typeOfDisplay: '',
                displayTechnology: '',
                screenSize: '',
                resolutionHorizontal: '',
                resolutionVertical: '',
            },
            financialForm: {
                purchaseDate: '',
                initialValue: '',
                expectedValue: '',
                annualStraightLine: '',
                monthlyStraightLine: '',
                currentValue: '',
                expectedReplacementDate: '',
            },
            
            get current() {
                return this.viewStack.length ? this.viewStack[this.viewStack.length - 1] : { type: null, id: null };
            },
            
            open(detail) {
                this.viewStack = [{ ...detail }];
                this.isOpen = true;
                this.syncCurrentView();
            },
            
            pushView(type, id) {
                this.viewStack.push({ type, id });
                this.syncCurrentView();
            },
            
            popView() {
                if (this.viewStack.length > 1) {
                    this.viewStack.pop();
                    this.syncCurrentView();
                } else {
                    this.close();
                }
            },

            syncCurrentView() {
                if (this.current.type === 'facility' && this.current.id) {
                    this.facilityEditing = false;
                    this.loadFacilityDetail(this.current.id);
                } else if (this.current.type === 'display' && this.current.id) {
                    this.activeDisplayTab = this.current.tab === 'settings' ? 'settings' : 'overview';
                    this.isEditingDisplaySettings = this.current.tab === 'settings' && !!this.current.editing;
                    this.displayStructureMapOpen = false;
                    this.workgroupStructureMapOpen = false;
                    this.workstationStructureMapOpen = false;
                    this.loadDisplayDetail(this.current.id);
                } else if (this.current.type === 'workgroup' && this.current.id) {
                    this.activeWorkgroupTab = 'overview';
                    this.workgroupSettingsEditing = false;
                    this.workgroupSettingsConfirmOpen = false;
                    this.displayStructureMapOpen = false;
                    this.workgroupStructureMapOpen = false;
                    this.workstationStructureMapOpen = false;
                    this.loadWorkgroupDetail(this.current.id);
                } else if (this.current.type === 'workstation' && this.current.id) {
                    this.activeWorkstationTab = this.current.tab === 'settings' ? 'settings' : 'overview';
                    this.activeWorkstationSettingsTab = this.current.settingsTab || 'application';
                    this.workstationSettingsEditing = this.current.tab === 'settings' && !!this.current.editing;
                    this.workstationSettingsConfirmOpen = false;
                    this.showWorkstationHierarchyEdit = false;
                    this.workstationMoveConfirmOpen = false;
                    this.displayStructureMapOpen = false;
                    this.workgroupStructureMapOpen = false;
                    this.workstationStructureMapOpen = false;
                    this.loadWorkstationDetail(this.current.id).then(() => {
                        if (this.activeWorkstationTab === 'settings') {
                            this.ensureWorkstationSettingsLoaded(true);
                        }
                    });
                } else {
                    this.facilityLoading = false;
                    this.facilityError = '';
                    this.displayLoading = false;
                    this.displayError = '';
                    this.workgroupLoading = false;
                    this.workgroupError = '';
                    this.workstationLoading = false;
                    this.workstationError = '';
                    this.workgroupSettingsConfirmOpen = false;
                    this.workstationSettingsConfirmOpen = false;
                    this.showWorkstationHierarchyEdit = false;
                    this.workstationMoveConfirmOpen = false;
                    this.displayStructureMapOpen = false;
                    this.workgroupStructureMapOpen = false;
                    this.workstationStructureMapOpen = false;
                }

                setTimeout(() => lucide.createIcons(), 50);
            },

            async loadFacilityDetail(id) {
                this.facilityLoading = true;
                this.facilityError = '';

                try {
                    const response = await Perfectlum.request(`/api/facility-modal/${id}`);
                    this.facilityDetail = response;
                    this.facilityForm = {
                        name: response.name || '',
                        description: response.description || '',
                        location: response.location || '',
                        timezone: response.timezone || '',
                    };
                } catch (error) {
                    this.facilityDetail = null;
                    this.facilityError = error.message || 'Facility detail could not be loaded.';
                } finally {
                    this.facilityLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            beginFacilityEdit() {
                if (!this.facilityDetail?.permissions?.edit) {
                    return;
                }
                this.facilityEditing = true;
                this.$nextTick(() => lucide.createIcons());
            },

            async cancelFacilityEdit() {
                this.facilityEditing = false;
                if (this.current.id) {
                    await this.loadFacilityDetail(this.current.id);
                }
                this.$nextTick(() => lucide.createIcons());
            },

            confirmSaveFacility() {
                if (this.savingFacility) {
                    return;
                }

                this.saveFacility();
            },

            async saveFacility() {
                if (!this.current.id) {
                    return;
                }

                this.savingFacility = true;
                try {
                    const formData = new FormData();
                    formData.append('_token', this.csrfToken());
                    formData.append('name', this.facilityForm.name || '');
                    formData.append('description', this.facilityForm.description || '');
                    formData.append('location', this.facilityForm.location || '');
                    formData.append('timezone', this.facilityForm.timezone || '');

                    await Perfectlum.postForm(`/api/facility-modal/${this.current.id}/save`, formData);
                    notify('success', 'Facility updated successfully.');
                    this.facilityEditing = false;
                    await this.loadFacilityDetail(this.current.id);
                } catch (error) {
                    notify('failed', error.message || 'Failed to update facility.');
                } finally {
                    this.savingFacility = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            async loadWorkgroupDetail(id) {
                this.workgroupLoading = true;
                this.workgroupError = '';

                try {
                    const response = await Perfectlum.request(`/api/workgroup-modal/${id}`);
                    this.workgroupDetail = response;
                    this.workgroupSettingsForm = {
                        name: response.settings?.name || '',
                        address: response.settings?.address || '',
                        phone: response.settings?.phone || '',
                        facility_id: response.settings?.facility_id || '',
                    };
                } catch (error) {
                    this.workgroupDetail = null;
                    this.workgroupError = error.message || 'Workgroup detail could not be loaded.';
                } finally {
                    this.workgroupLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            beginWorkgroupSettingsEdit() {
                if (!this.workgroupDetail?.permissions?.edit) {
                    return;
                }
                this.workgroupSettingsEditing = true;
                this.$nextTick(() => lucide.createIcons());
            },

            async cancelWorkgroupSettingsEdit() {
                this.workgroupSettingsEditing = false;
                this.workgroupSettingsConfirmOpen = false;
                if (this.current.id) {
                    await this.loadWorkgroupDetail(this.current.id);
                }
                this.$nextTick(() => lucide.createIcons());
            },

            confirmSaveWorkgroupSettings() {
                if (this.savingWorkgroupSettings) {
                    return;
                }

                this.workgroupSettingsConfirmOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            closeWorkgroupSettingsConfirm() {
                this.workgroupSettingsConfirmOpen = false;
                this.$nextTick(() => lucide.createIcons());
            },

            filteredWorkgroupFacilityOptions() {
                const options = this.sortedNamedOptions(this.workgroupDetail?.settings?.facilities || []);
                const term = (this.workgroupOptionSearch.facilities || '').trim().toLowerCase();
                if (!term) {
                    return options;
                }

                return options.filter((item) => String(item.name || '').toLowerCase().includes(term));
            },

            workgroupFacilityOptionHint() {
                const total = (this.workgroupDetail?.settings?.facilities || []).length;
                const filtered = this.filteredWorkgroupFacilityOptions().length;
                if (filtered === 0) {
                    return @js(__('No options found'));
                }

                return filtered === total ? `${total} options` : `${filtered} of ${total} options`;
            },

            selectedWorkgroupFacilityName() {
                return this.findMoveOptionName(this.workgroupDetail?.settings?.facilities || [], this.workgroupSettingsForm.facility_id);
            },

            async saveWorkgroupSettings() {
                if (!this.current.id) {
                    return;
                }

                this.savingWorkgroupSettings = true;
                this.workgroupSettingsConfirmOpen = false;
                try {
                    const formData = new FormData();
                    formData.append('_token', this.csrfToken());
                    formData.append('name', this.workgroupSettingsForm.name || '');
                    formData.append('address', this.workgroupSettingsForm.address || '');
                    formData.append('phone', this.workgroupSettingsForm.phone || '');
                    if (this.workgroupDetail?.permissions?.changeFacility) {
                        formData.append('facility_id', this.workgroupSettingsForm.facility_id || '');
                    }

                    await Perfectlum.postForm(`/api/workgroup-modal/${this.current.id}/save`, formData);
                    notify('success', 'Workgroup settings updated.');
                    this.workgroupSettingsEditing = false;
                    await this.loadWorkgroupDetail(this.current.id);
                } catch (error) {
                    notify('failed', error.message || 'Failed to update workgroup settings.');
                } finally {
                    this.savingWorkgroupSettings = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            async loadWorkstationDetail(id) {
                this.workstationLoading = true;
                this.workstationError = '';
                this.workstationSettingsReady = false;
                this.workstationSettingsError = '';

                try {
                    this.workstationDetail = await Perfectlum.request(`/api/workstation-modal/${id}`);
                } catch (error) {
                    this.workstationDetail = null;
                    this.workstationError = error.message || 'Workstation detail could not be loaded.';
                } finally {
                    this.workstationLoading = false;
                    setTimeout(() => lucide.createIcons(), 50);
                }
            },

            workstationOverviewStats() {
                const displays = this.workstationDetail?.displays || [];
                return {
                    totalDisplays: displays.length,
                    healthyDisplays: displays.filter((display) => display.statusTone === 'success').length,
                    attentionDisplays: displays.filter((display) => display.statusTone !== 'success').length,
                };
            },

            workstationStructureMapStats() {
                const items = this.current.type === 'workgroup'
                    ? (this.workgroupDetail?.structure?.workgroups || [])
                    : (this.workstationDetail?.structure?.workstations || []);
                return {
                    all: items.length,
                    attention: items.filter((item) => Number(item.attentionCount || 0) > 0).length,
                    healthy: items.filter((item) => Number(item.attentionCount || 0) === 0).length,
                };
            },

            openWorkstationSettingsTab() {
                this.activeWorkstationTab = 'settings';
                this.ensureWorkstationSettingsLoaded();
            },

            setWorkstationSettingsSubtab(tab) {
                this.activeWorkstationSettingsTab = tab;
                this.ensureWorkstationSettingsLoaded();
            },

            async ensureWorkstationSettingsLoaded(force = false) {
                if (!this.current.id) {
                    return;
                }

                if (this.workstationSettingsReady && !force) {
                    return;
                }

                await this.loadWorkstationSettings(this.current.id);
            },

            parseSettingsOptions(raw) {
                if (!raw) {
                    return [];
                }

                let parsed = raw;
                if (typeof raw === 'string') {
                    try {
                        parsed = JSON.parse(raw);
                    } catch (error) {
                        return [];
                    }
                }

                if (Array.isArray(parsed)) {
                    return parsed.map((item) => {
                        if (typeof item === 'object') {
                            return { value: String(item.key ?? item.value ?? ''), label: item.value ?? item.label ?? item.key ?? '' };
                        }
                        return { value: String(item), label: item };
                    });
                }

                const options = Object.entries(parsed).map(([value, label]) => ({
                    value: String(value),
                    label: label === '' ? (value === '' ? 'Select' : value) : label,
                }));

                return options.sort((left, right) => {
                    const leftBlank = left.value === '' ? 1 : 0;
                    const rightBlank = right.value === '' ? 1 : 0;
                    if (leftBlank !== rightBlank) {
                        return rightBlank - leftBlank;
                    }

                    return left.label.localeCompare(right.label, undefined, { sensitivity: 'base' });
                });
            },

            workstationOptionList(field) {
                return this.parseSettingsOptions(this.workstationSettingsOptions[field]);
            },

            filteredWorkstationOptionList(field, options = null) {
                const list = options || this.workstationOptionList(field);
                const term = (this.workstationOptionSearch[field] || '').trim().toLowerCase();
                if (!term) {
                    return list;
                }

                return list.filter((option) => {
                    return String(option.label).toLowerCase().includes(term) || String(option.value).toLowerCase().includes(term);
                });
            },

            workstationOptionLabel(field, value) {
                const options = this.workstationOptionList(field);
                const match = options.find((option) => String(option.value) === String(value ?? ''));
                return match?.label || (value === '' || value == null ? '-' : String(value));
            },

            dropdownDisplayLabel(field, value, options = null, placeholder = 'Select') {
                const list = options || this.workstationOptionList(field);
                const match = list.find((option) => String(option.value) === String(value ?? ''));
                return match?.label || (value === '' || value == null ? placeholder : String(value));
            },

            workstationOptionHint(field, options = null) {
                const total = (options || this.workstationOptionList(field)).length;
                const filtered = this.filteredWorkstationOptionList(field, options).length;
                if (filtered === 0) {
                    return @js(__('No options found'));
                }

                return filtered === total ? `${total} options` : `${filtered} of ${total} options`;
            },

            toggleInlineSelect(key) {
                this.activeInlineSelect = this.activeInlineSelect === key ? null : key;
                if (this.activeInlineSelect === key) {
                    this.$nextTick(() => {
                        this.$refs[`search-${key}`]?.focus();
                    });
                }
            },

            closeInlineSelect() {
                this.activeInlineSelect = null;
            },

            isInlineSelectOpen(key) {
                return this.activeInlineSelect === key;
            },

            sortedNamedOptions(options = []) {
                return [...options].sort((left, right) => String(left.name || '').localeCompare(String(right.name || ''), undefined, { sensitivity: 'base' }));
            },

            filteredWorkstationMoveOptions(type) {
                const options = this.sortedNamedOptions(this.workstationMoveOptions[type] || []);
                const term = (this.workstationMoveSearch[type] || '').trim().toLowerCase();
                if (!term) {
                    return options;
                }

                return options.filter((item) => String(item.name || '').toLowerCase().includes(term));
            },

            workstationMoveOptionHint(type) {
                const total = (this.workstationMoveOptions[type] || []).length;
                const filtered = this.filteredWorkstationMoveOptions(type).length;
                if (filtered === 0) {
                    return @js(__('No options found'));
                }

                return filtered === total ? `${total} options` : `${filtered} of ${total} options`;
            },

            selectedWorkstationFacilityName() {
                return this.findMoveOptionName(this.workstationMoveOptions.facilities, this.workstationMoveForm.facilityId);
            },

            selectedWorkstationWorkgroupName() {
                return this.findMoveOptionName(this.workstationMoveOptions.workgroups, this.workstationMoveForm.workgroupId);
            },

            filteredMoveOptions(type) {
                const options = this.sortedNamedOptions(this.moveOptions[type] || []);
                const term = (this.moveOptionSearch[type] || '').trim().toLowerCase();
                if (!term) {
                    return options;
                }

                return options.filter((item) => String(item.name || '').toLowerCase().includes(term));
            },

            moveOptionHint(type) {
                const total = (this.moveOptions[type] || []).length;
                const filtered = this.filteredMoveOptions(type).length;
                if (filtered === 0) {
                    return @js(__('No options found'));
                }

                return filtered === total ? `${total} options` : `${filtered} of ${total} options`;
            },

            workstationBooleanLabel(value, trueLabel = 'Enabled', falseLabel = 'Disabled') {
                return value === true || value === 1 || value === '1' || value === 'true' ? trueLabel : falseLabel;
            },

            async loadWorkstationSettings(id) {
                this.workstationSettingsLoading = true;
                this.workstationSettingsError = '';

                try {
                    const response = await Perfectlum.request(`/app-settings/ws-${id}`);
                    const data = response?.data || {};
                    this.workstationSettingsOptions = response?.options || {};

                    const whiteLevelOptions = this.workstationOptionList('WhiteLevel_u_extcombo');
                    const whiteLevelValue = data.WhiteLevel ?? '';
                    const matchingWhiteLevel = whiteLevelOptions.find((option) => String(option.value) === String(whiteLevelValue));

                    this.workstationSettingsForm = {
                        application: {
                            name: this.workstationDetail?.name || '',
                            workgroup_id: String(data.workgroup_id ?? this.workstationDetail?.workgroup?.id ?? ''),
                            units: data.units ?? '',
                            LumUnits: data.LumUnits ?? '',
                            AmbientLight: data.AmbientLight ?? '',
                            AmbientStable: data.AmbientStable ?? '',
                            PutDisplaysToEnergySaveMode: data.PutDisplaysToEnergySaveMode === 'true' || data.PutDisplaysToEnergySaveMode === '1' || data.PutDisplaysToEnergySaveMode === true,
                            StartEnergySaveMode: data.StartEnergySaveMode ?? '',
                            EndEnergySaveMode: data.EndEnergySaveMode ?? '',
                        },
                        calibration: {
                            CalibrationPresents: data.CalibrationPresents ?? '',
                            CalibrationType: data.CalibrationType ?? '',
                            ColorTemperatureAdjustment: data.ColorTemperatureAdjustment ?? '',
                            ColorTemperatureAdjustment_ext: data.ColorTemperatureAdjustment_ext ?? '',
                            WhiteLevel_u_extcombo: matchingWhiteLevel ? String(whiteLevelValue) : (whiteLevelValue ? 'custom' : ''),
                            WhiteLevel_u_input: matchingWhiteLevel ? '' : whiteLevelValue,
                            WhiteLevel: whiteLevelValue,
                            SetWhiteLevel: data.SetWhiteLevel ?? '',
                            BlackLevel: data.BlackLevel ?? '',
                            SetBlackLevel: data.SetBlackLevel ?? '',
                            Gamma: data.Gamma ?? '',
                            gamut_name: data.gamut_name ?? '',
                            CreateICCICMProfile: data.CreateICCICMProfile === 'true' || data.CreateICCICMProfile === '1' || data.CreateICCICMProfile === true,
                        },
                        qa: {
                            UsedRegulation: data.UsedRegulation ?? '',
                            UsedClassification: data.UsedClassification ?? '',
                            UsedClassificationForLastScheduling: data.UsedClassificationForLastScheduling ?? '',
                            UsedRegulationForLastScheduling: data.UsedRegulationForLastScheduling ?? '',
                            bodyRegion: data.bodyRegion ?? '',
                            AutoDailyTests: data.AutoDailyTests === 'true' || data.AutoDailyTests === '1' || data.AutoDailyTests === true,
                        },
                        location: {
                            Facility: data.Facility ?? '',
                            Department: data.Department ?? '',
                            Room: data.Room ?? '',
                            ResponsiblePersonName: data.ResponsiblePersonName ?? '',
                            ResponsiblePersonCity: data.ResponsiblePersonCity ?? '',
                            ResponsiblePersonAddress: data.ResponsiblePersonAddress ?? '',
                            ResponsiblePersonEmail: data.ResponsiblePersonEmail ?? '',
                            ResponsiblePersonPhoneNumber: data.ResponsiblePersonPhoneNumber ?? '',
                        },
                    };

                    await this.refreshWorkstationClassificationOptions(false);
                    this.workstationSettingsReady = true;
                } catch (error) {
                    this.workstationSettingsError = error.message || 'Workstation settings could not be loaded.';
                    this.workstationSettingsReady = false;
                } finally {
                    this.workstationSettingsLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            async refreshWorkstationClassificationOptions(resetSelection = true) {
                const regulation = this.workstationSettingsForm?.qa?.UsedRegulation;
                if (!this.current.id || !regulation) {
                    this.workstationQaClassificationOptions = [];
                    return;
                }

                try {
                    const response = await Perfectlum.request(`/app-settings/get/categories?id=${encodeURIComponent(`ws-${this.current.id}`)}&regulation=${encodeURIComponent(regulation)}`);
                    this.workstationQaClassificationOptions = this.parseSettingsOptions(response);
                    if (resetSelection && this.workstationQaClassificationOptions.length) {
                        this.workstationSettingsForm.qa.UsedClassification = this.workstationQaClassificationOptions[0].value;
                    }
                } catch (error) {
                    this.workstationQaClassificationOptions = [];
                }
            },

            beginWorkstationSettingsEdit() {
                if (!this.workstationDetail?.permissions?.edit) {
                    return;
                }
                this.workstationSettingsEditing = true;
                this.$nextTick(() => lucide.createIcons());
            },

            async cancelWorkstationSettingsEdit() {
                this.workstationSettingsEditing = false;
                this.workstationSettingsConfirmOpen = false;
                await this.ensureWorkstationSettingsLoaded(true);
                this.$nextTick(() => lucide.createIcons());
            },

            confirmSaveWorkstationSettings() {
                if (this.savingWorkstationSettings) {
                    return;
                }

                this.workstationSettingsConfirmOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            closeWorkstationSettingsConfirm() {
                this.workstationSettingsConfirmOpen = false;
                this.$nextTick(() => lucide.createIcons());
            },

            async openWorkstationHierarchyEdit() {
                if (!this.current.id || !this.workstationDetail?.permissions?.changeWorkgroup) {
                    return;
                }

                this.showWorkstationHierarchyEdit = true;
                this.workstationMoveLoading = true;

                try {
                    const response = await Perfectlum.request(`/api/workstation-modal/${this.current.id}/move-options`);
                    this.workstationMoveOptions.facilities = this.sortedNamedOptions(response.facilities || []);
                    this.workstationMoveOptions.workgroups = this.sortedNamedOptions(response.workgroups || []);
                    this.workstationMoveForm.facilityId = response.current?.facilityId ? String(response.current.facilityId) : '';
                    this.workstationMoveForm.workgroupId = response.current?.workgroupId ? String(response.current.workgroupId) : '';
                } catch (error) {
                    notify('failed', error.message || 'Failed to load workstation hierarchy options.');
                    this.showWorkstationHierarchyEdit = false;
                } finally {
                    this.workstationMoveLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            closeWorkstationHierarchyEdit() {
                this.showWorkstationHierarchyEdit = false;
                this.workstationMoveConfirmOpen = false;
                this.workstationMoveLoading = false;
                this.workstationMoveOptions = { facilities: [], workgroups: [] };
                this.workstationMoveSearch = { facilities: '', workgroups: '' };
                this.workstationMoveForm = { facilityId: '', workgroupId: '' };
                this.$nextTick(() => lucide.createIcons());
            },

            async changeWorkstationMoveFacility() {
                this.workstationMoveForm.workgroupId = '';
                this.workstationMoveSearch.workgroups = '';
                this.workstationMoveOptions.workgroups = [];

                if (!this.workstationMoveForm.facilityId) {
                    return;
                }

                this.workstationMoveLoading = true;
                try {
                    const response = await Perfectlum.request(`/api/workstation-modal/workgroups/${this.workstationMoveForm.facilityId}`);
                    this.workstationMoveOptions.workgroups = this.sortedNamedOptions(response || []);
                } catch (error) {
                    notify('failed', error.message || 'Failed to load workgroups.');
                } finally {
                    this.workstationMoveLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            confirmWorkstationMove() {
                if (!this.workstationMoveForm.workgroupId || this.workstationMoveLoading) {
                    return;
                }

                this.workstationMoveConfirmOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            closeWorkstationMoveConfirm() {
                this.workstationMoveConfirmOpen = false;
                this.$nextTick(() => lucide.createIcons());
            },

            async saveWorkstationHierarchyMove() {
                if (!this.current.id || !this.workstationMoveForm.workgroupId) {
                    return;
                }

                this.workstationMoveLoading = true;
                try {
                    const formData = new FormData();
                    formData.append('_token', this.csrfToken());
                    formData.append('workgroup_id', this.workstationMoveForm.workgroupId);

                    await Perfectlum.postForm(`/api/workstation-modal/${this.current.id}/move`, formData);
                    notify('success', 'Workstation hierarchy updated.');
                    this.workstationMoveConfirmOpen = false;
                    this.showWorkstationHierarchyEdit = false;
                    await Promise.all([
                        this.loadWorkstationDetail(this.current.id),
                        this.loadWorkstationSettings(this.current.id),
                    ]);
                } catch (error) {
                    notify('failed', error.message || 'Failed to move workstation.');
                } finally {
                    this.workstationMoveLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            workstationReadonlyItems(tab) {
                const application = this.workstationSettingsForm.application || {};
                const calibration = this.workstationSettingsForm.calibration || {};
                const qa = this.workstationSettingsForm.qa || {};
                const location = this.workstationSettingsForm.location || {};
                const whiteLevelValue = calibration.WhiteLevel_u_extcombo === 'custom' ? calibration.WhiteLevel_u_input : calibration.WhiteLevel_u_extcombo;

                const map = {
                    application: [
                        { label: 'Workstation Name', value: application.name || this.workstationDetail?.name || '-' },
                        { label: 'Workgroup', value: this.workstationOptionLabel('workgroup_id', application.workgroup_id) },
                        { label: @js(__('Units of Length')), value: this.workstationOptionLabel('units', application.units) },
                        { label: @js(__('Units of Luminance')), value: this.workstationOptionLabel('LumUnits', application.LumUnits) },
                        { label: 'Veiling Luminance', value: application.AmbientLight || '-' },
                        { label: @js(__('Ambient Conditions Stable')), value: this.workstationOptionLabel('AmbientStable', application.AmbientStable) },
                        { label: 'Energy Save Mode', value: this.workstationBooleanLabel(application.PutDisplaysToEnergySaveMode) },
                        { label: 'Energy Save Start', value: application.StartEnergySaveMode || '-' },
                        { label: 'Energy Save End', value: application.EndEnergySaveMode || '-' },
                    ],
                    calibration: [
                        { label: @js(__('Preset')), value: this.workstationOptionLabel('CalibrationPresents', calibration.CalibrationPresents) },
                        { label: @js(__('Luminance Response')), value: this.workstationOptionLabel('CalibrationType', calibration.CalibrationType) },
                        { label: 'Color Temperature', value: calibration.ColorTemperatureAdjustment === '20' ? (calibration.ColorTemperatureAdjustment_ext || 'Custom') : this.workstationOptionLabel('ColorTemperatureAdjustment', calibration.ColorTemperatureAdjustment) },
                        { label: @js(__('Max Luminance')), value: this.workstationOptionLabel('WhiteLevel_u_extcombo', whiteLevelValue) },
                        { label: @js(__('Gamut')), value: this.workstationOptionLabel('gamut_name', calibration.gamut_name) },
                        { label: 'ICC Profile', value: this.workstationBooleanLabel(calibration.CreateICCICMProfile, 'Create', 'Disabled') },
                    ],
                    qa: [
                        { label: @js(__('Regulation')), value: this.workstationOptionLabel('UsedRegulation', qa.UsedRegulation) },
                        { label: @js(__('Display Category')), value: this.workstationQaClassificationOptions.find((option) => String(option.value) === String(qa.UsedClassification))?.label || qa.UsedClassification || '-' },
                        { label: 'Body Region', value: qa.bodyRegion || '-' },
                        { label: 'Daily Tests', value: this.workstationBooleanLabel(qa.AutoDailyTests, 'Automatic', 'Manual') },
                    ],
                    location: this.workstationLocationFields.map((field) => ({
                        label: field.label,
                        value: location[field.key] || '-',
                    })),
                };

                return map[tab] || [];
            },

            async saveWorkstationSettings() {
                if (!this.current.id) {
                    return;
                }

                this.savingWorkstationSettings = true;
                this.workstationSettingsConfirmOpen = false;
                const actionMap = {
                    application: 'app',
                    calibration: 'dc',
                    qa: 'qa',
                    location: 'location',
                };
                const action = actionMap[this.activeWorkstationSettingsTab];
                const formData = new FormData();
                formData.append('_token', this.csrfToken());

                if (this.activeWorkstationSettingsTab === 'application') {
                    Object.entries(this.workstationSettingsForm.application).forEach(([key, value]) => {
                        if (key === 'workgroup_id' && !this.workstationDetail?.permissions?.changeWorkgroup) {
                            return;
                        }
                        formData.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : (value ?? ''));
                    });
                }

                if (this.activeWorkstationSettingsTab === 'calibration') {
                    const calibration = this.workstationSettingsForm.calibration;
                    const whiteLevel = calibration.WhiteLevel_u_extcombo === 'custom' ? (calibration.WhiteLevel_u_input || '') : (calibration.WhiteLevel_u_extcombo || '');
                    formData.append('CalibrationPresents', calibration.CalibrationPresents || '');
                    formData.append('CalibrationType', calibration.CalibrationType || '');
                    formData.append('ColorTemperatureAdjustment', calibration.ColorTemperatureAdjustment || '');
                    formData.append('ColorTemperatureAdjustment_ext', calibration.ColorTemperatureAdjustment_ext || '');
                    formData.append('WhiteLevel_u_extcombo', calibration.WhiteLevel_u_extcombo || '');
                    formData.append('WhiteLevel_u_input', calibration.WhiteLevel_u_input || '');
                    formData.append('WhiteLevel', whiteLevel);
                    formData.append('SetWhiteLevel', calibration.SetWhiteLevel || whiteLevel);
                    formData.append('BlackLevel', calibration.BlackLevel || '');
                    formData.append('SetBlackLevel', calibration.SetBlackLevel || '');
                    formData.append('Gamma', calibration.Gamma || '');
                    formData.append('gamut_name', calibration.gamut_name || '');
                    formData.append('CreateICCICMProfile', calibration.CreateICCICMProfile ? '1' : '0');
                }

                if (this.activeWorkstationSettingsTab === 'qa') {
                    const qa = this.workstationSettingsForm.qa;
                    formData.append('UsedRegulation', qa.UsedRegulation || '');
                    formData.append('UsedClassification', qa.UsedClassification || '');
                    formData.append('UsedClassificationForLastScheduling', qa.UsedClassificationForLastScheduling || qa.UsedClassification || '');
                    formData.append('UsedRegulationForLastScheduling', qa.UsedRegulationForLastScheduling || qa.UsedRegulation || '');
                    formData.append('bodyRegion', qa.bodyRegion || '');
                    formData.append('AutoDailyTests', qa.AutoDailyTests ? '1' : '0');
                }

                if (this.activeWorkstationSettingsTab === 'location') {
                    Object.entries(this.workstationSettingsForm.location).forEach(([key, value]) => {
                        formData.append(key, value ?? '');
                    });
                }

                try {
                    await Perfectlum.postForm(`/app-settings/save/${action}/ws-${this.current.id}`, formData);
                    notify('success', 'Workstation settings updated.');
                    this.workstationSettingsEditing = false;
                    await Promise.all([
                        this.loadWorkstationDetail(this.current.id),
                        this.loadWorkstationSettings(this.current.id),
                    ]);
                } catch (error) {
                    notify('failed', error.message || 'Failed to update workstation settings.');
                } finally {
                    this.savingWorkstationSettings = false;
                }
            },

            async loadDisplayDetail(id, options = {}) {
                const preserveView = !!options.preserveView;
                const syncForms = options.syncForms !== false;

                if (preserveView) {
                    this.historyLoading = true;
                } else {
                    this.displayLoading = true;
                    this.displayError = '';
                }

                try {
                    const response = await Perfectlum.request(`/api/display-modal/${id}?period=${encodeURIComponent(this.displayPeriod)}`);

                    if (preserveView && this.displayDetail) {
                        this.displayDetail = {
                            ...this.displayDetail,
                            history: response.history,
                            latestError: response.latestError,
                            lastSync: response.lastSync,
                            statusLabel: response.statusLabel,
                            statusTone: response.statusTone,
                            connectedLabel: response.connectedLabel,
                            links: response.links,
                        };
                    } else {
                        this.displayDetail = response;
                    }

                    if (this.selectedPerformanceTrendBucket) {
                        const nextBucket = (response.history?.timeline || []).find((bucket) => bucket.key === this.selectedPerformanceTrendBucket.key);
                        this.selectedPerformanceTrendBucket = nextBucket || null;
                    }

                    if (syncForms) {
                        this.syncDisplayForms();
                    }
                } catch (error) {
                    if (!preserveView) {
                        this.displayDetail = null;
                        this.displayError = error.message || 'Display detail could not be loaded.';
                    } else {
                        notify('failed', error.message || 'Failed to refresh display history.');
                    }
                } finally {
                    this.displayLoading = false;
                    this.historyLoading = false;
                    setTimeout(() => lucide.createIcons(), 50);
                }
            },

            changeDisplayPeriod(period) {
                if (this.displayPeriod === period || !this.current.id) {
                    return;
                }

                this.hidePerformanceTrendTooltip();
                this.displayPeriod = period;
                this.loadDisplayDetail(this.current.id, { preserveView: true, syncForms: false });
            },

            syncDisplayForms() {
                if (!this.displayDetail) {
                    return;
                }

                this.displayForm = {
                    exclude: !!this.displayDetail.exclude,
                    graphicboardOnly: !!this.displayDetail.graphicboardOnly,
                    internalSensor: !!this.displayDetail.internalSensor,
                    currentLut: this.displayDetail.currentLut === '-' ? '' : (this.displayDetail.currentLut || ''),
                    installationDate: this.displayDetail.installationDate === '-' ? '' : (this.displayDetail.installationDate || ''),
                    manufacturer: this.displayDetail.manufacturer === '-' ? '' : (this.displayDetail.manufacturer || ''),
                    model: this.displayDetail.model === '-' ? '' : (this.displayDetail.model || ''),
                    serial: this.displayDetail.serial === '-' ? '' : (this.displayDetail.serial || ''),
                    inventoryNumber: this.displayDetail.inventoryNumber === '-' ? '' : (this.displayDetail.inventoryNumber || ''),
                    typeOfDisplay: this.displayDetail.typeOfDisplay === '-' ? '' : (this.displayDetail.typeOfDisplay || ''),
                    displayTechnology: this.displayDetail.displayTechnology === '-' ? '' : (this.displayDetail.displayTechnology || ''),
                    screenSize: this.displayDetail.screenSize === '-' ? '' : (this.displayDetail.screenSize || ''),
                    resolutionHorizontal: (this.displayDetail.resolution || '').split(' x ')[0] || '',
                    resolutionVertical: (this.displayDetail.resolution || '').split(' x ')[1] || '',
                };

                this.financialForm = {
                    purchaseDate: this.displayDetail.purchaseDate === '-' ? '' : (this.displayDetail.purchaseDate || ''),
                    initialValue: this.displayDetail.initialValue === '-' ? '' : (this.displayDetail.initialValue || ''),
                    expectedValue: this.displayDetail.expectedValue === '-' ? '' : (this.displayDetail.expectedValue || ''),
                    annualStraightLine: this.displayDetail.annualStraightLine === '-' ? '' : (this.displayDetail.annualStraightLine || ''),
                    monthlyStraightLine: this.displayDetail.monthlyStraightLine === '-' ? '' : (this.displayDetail.monthlyStraightLine || ''),
                    currentValue: this.displayDetail.currentValue === '-' ? '' : (this.displayDetail.currentValue || ''),
                    expectedReplacementDate: this.displayDetail.expectedReplacementDate === '-' ? '' : (this.displayDetail.expectedReplacementDate || ''),
                };
            },

            cancelDisplayEditing() {
                this.isEditingDisplaySettings = false;
                this.settingsConfirmOpen = false;
                this.syncDisplayForms();
                this.$nextTick(() => lucide.createIcons());
            },

            async openDisplayStructureMap() {
                this.displayStructureMapOpen = true;
                this.workgroupStructureMapOpen = false;
                this.workstationStructureMapOpen = false;
                this.structureMapFilter = 'all';
                this.$nextTick(async () => {
                    await this.renderStructureMapGraph();
                    lucide.createIcons();
                });
            },

            async openWorkstationStructureMap() {
                this.workstationStructureMapOpen = true;
                this.displayStructureMapOpen = false;
                this.workgroupStructureMapOpen = false;
                this.structureMapFilter = 'all';
                this.structureMapExpandedWorkstationId = this.current.type === 'workstation' ? this.current.id : null;
                this.$nextTick(async () => {
                    await this.renderStructureMapGraph();
                    lucide.createIcons();
                });
            },

            async openWorkgroupStructureMap() {
                this.workgroupStructureMapOpen = true;
                this.displayStructureMapOpen = false;
                this.workstationStructureMapOpen = false;
                this.structureMapFilter = 'all';
                this.structureMapExpandedWorkstationId = null;
                this.structureMapExpandedWorkgroupId = null;
                this.$nextTick(async () => {
                    await this.renderStructureMapGraph();
                    lucide.createIcons();
                });
            },

            setStructureMapFilter(filter) {
                if (!['workstation', 'workgroup'].includes(this.current.type) || this.structureMapFilter === filter) {
                    return;
                }

                this.structureMapFilter = filter;
                this.$nextTick(async () => {
                    await this.renderStructureMapGraph(true);
                    lucide.createIcons();
                });
            },

            structureMapTitle() {
                if (this.current.type === 'workstation') {
                    return @js(__('Workstation Hierarchy Map'));
                }

                if (this.current.type === 'workgroup') {
                    return @js(__('Workgroup Hierarchy Map'));
                }

                return @js(__('Display Hierarchy Map'));
            },

            structureMapDescription() {
                if (this.current.type === 'workgroup') {
                    return @js(__('Facility to workgroup path with sibling workgroups in the same facility. Drag to pan, use zoom controls or mouse wheel.'));
                }

                if (this.current.type === 'workstation') {
                    return @js(__('Facility to workstation path with sibling workstations in the same workgroup. Drag to pan, use zoom controls or mouse wheel.'));
                }

                return @js(__('Facility to workstation path with sibling displays on the selected workstation. Drag to pan, use zoom controls or mouse wheel.'));
            },

            structureMapAttentionLabel() {
                return @js(__('Needs Attention')) + ' (' + this.workstationStructureMapStats().attention + ')';
            },

            structureMapHealthyLabel() {
                return @js(__('Healthy')) + ' (' + this.workstationStructureMapStats().healthy + ')';
            },

            closeDisplayStructureMap() {
                this.displayStructureMapOpen = false;
                this.workgroupStructureMapOpen = false;
                this.workstationStructureMapOpen = false;
                this.structureMapFilter = 'all';
                this.structureMapExpandedWorkstationId = null;
                this.structureMapExpandedWorkgroupId = null;
                this.destroyStructureMapGraph();
                this.$nextTick(() => lucide.createIcons());
            },

            async renderStructureMapGraph(preserveViewport = false) {
                let structure = this.current.type === 'workstation'
                    ? this.workstationDetail?.structure
                    : (this.current.type === 'workgroup'
                        ? this.workgroupDetail?.structure
                        : this.displayDetail?.structure);

                if (this.current.type === 'workstation' && structure) {
                    structure = {
                        ...structure,
                        workstations: (structure.workstations || []).filter((workstation) => {
                            if (this.structureMapFilter === 'attention') {
                                return Number(workstation.attentionCount || 0) > 0;
                            }
                            if (this.structureMapFilter === 'healthy') {
                                return Number(workstation.attentionCount || 0) === 0;
                            }
                            return true;
                        }),
                    };
                }

                if (this.current.type === 'workgroup' && structure) {
                    const filterWorkstations = (workstations = []) => workstations.filter((workstation) => {
                        if (this.structureMapFilter === 'attention') {
                            return Number(workstation.attentionCount || 0) > 0;
                        }
                        if (this.structureMapFilter === 'healthy') {
                            return Number(workstation.attentionCount || 0) === 0;
                        }
                        return true;
                    });

                    structure = {
                        ...structure,
                        workgroups: (structure.workgroups || [])
                            .filter((workgroup) => {
                                if (this.structureMapFilter === 'attention') {
                                    return Number(workgroup.attentionCount || 0) > 0;
                                }
                                if (this.structureMapFilter === 'healthy') {
                                    return Number(workgroup.attentionCount || 0) === 0;
                                }
                                return true;
                            })
                            .map((workgroup) => ({
                                ...workgroup,
                                workstations: filterWorkstations(workgroup.workstations || []),
                            })),
                    };

                    if (
                        this.structureMapExpandedWorkgroupId &&
                        !structure.workgroups.some((workgroup) => String(workgroup.id) === String(this.structureMapExpandedWorkgroupId))
                    ) {
                        this.structureMapExpandedWorkgroupId = null;
                        this.structureMapExpandedWorkstationId = null;
                    }

                    if (this.structureMapExpandedWorkgroupId && this.structureMapExpandedWorkstationId) {
                        const expandedGroup = structure.workgroups.find((workgroup) => String(workgroup.id) === String(this.structureMapExpandedWorkgroupId));
                        if (!expandedGroup || !(expandedGroup.workstations || []).some((workstation) => String(workstation.id) === String(this.structureMapExpandedWorkstationId))) {
                            this.structureMapExpandedWorkstationId = null;
                        }
                    }
                }

                if (!this.$refs.structureGraphContainer || !structure) {
                    return;
                }

                const previousViewport = preserveViewport && this.structureGraphInstance?.getViewport
                    ? this.structureGraphInstance.getViewport()
                    : null;

                this.destroyStructureMapGraph();

                this.structureGraphInstance = await Perfectlum.createStructureMapGraph({
                    container: this.$refs.structureGraphContainer,
                    structure,
                    expandedWorkstationId: this.current.type === 'display'
                        ? null
                        : this.structureMapExpandedWorkstationId,
                    expandedWorkgroupId: this.current.type === 'workgroup'
                        ? this.structureMapExpandedWorkgroupId
                        : null,
                    onExpandedWorkstationChange: (workstationId) => {
                        this.structureMapExpandedWorkstationId = workstationId ? Number(workstationId) : null;
                    },
                    onExpandedWorkgroupChange: (workgroupId) => {
                        this.structureMapExpandedWorkgroupId = workgroupId ? Number(workgroupId) : null;
                        this.structureMapExpandedWorkstationId = null;
                    },
                    onOpenDisplay: (id) => {
                        this.closeDisplayStructureMap();
                        this.pushView('display', Number(id));
                    },
                    onZoomChange: (zoom) => {
                        this.structureMap.zoom = zoom;
                    },
                });

                if (previousViewport && this.structureGraphInstance?.setViewport) {
                    this.structureGraphInstance.setViewport(previousViewport);
                    this.structureMap.zoom = previousViewport.zoom || 1;
                } else {
                    this.structureMap.zoom = 1;
                }
            },

            destroyStructureMapGraph() {
                if (this.structureGraphInstance) {
                    this.structureGraphInstance.destroy();
                    this.structureGraphInstance = null;
                }
            },

            initializeStructureMapLayout() {
                return this.renderStructureMapGraph();
            },

            async autoLayoutStructureMap() {
                await this.renderStructureMapGraph();
                this.$nextTick(() => lucide.createIcons());
            },

            resetStructureMapView() {
                if (!this.structureGraphInstance) {
                    return;
                }

                this.structureGraphInstance.fit();
            },

            zoomStructureMapIn() {
                if (!this.structureGraphInstance) {
                    return;
                }

                this.structureGraphInstance.zoomIn();
            },

            zoomStructureMapOut() {
                if (!this.structureGraphInstance) {
                    return;
                }

                this.structureGraphInstance.zoomOut();
            },

            handleStructureMapWheel(event) {
                const delta = event.deltaY > 0 ? -0.08 : 0.08;
                this.structureMap.zoom = Math.max(0.6, Math.min(2.2, +(this.structureMap.zoom + delta).toFixed(2)));
            },

            startStructureMapDrag(event) {
                if (this.structureNodeDrag.active) {
                    return;
                }
                this.structureMap.dragging = true;
                this.structureMap.dragStartX = event.clientX;
                this.structureMap.dragStartY = event.clientY;
                this.structureMap.startPanX = this.structureMap.panX;
                this.structureMap.startPanY = this.structureMap.panY;
            },

            moveStructureMapDrag(event) {
                if (this.structureNodeDrag.active) {
                    const dx = (event.clientX - this.structureNodeDrag.startX) / this.structureMap.zoom;
                    const dy = (event.clientY - this.structureNodeDrag.startY) / this.structureMap.zoom;
                    const nextX = this.structureNodeDrag.originX + dx;
                    const nextY = this.structureNodeDrag.originY + dy;

                    if (this.structureNodeDrag.type === 'display') {
                        this.structureNodes.displays[this.structureNodeDrag.id] = {
                            ...this.structureNodes.displays[this.structureNodeDrag.id],
                            x: nextX,
                            y: nextY,
                        };
                    } else {
                        this.structureNodes[this.structureNodeDrag.type] = {
                            ...this.structureNodes[this.structureNodeDrag.type],
                            x: nextX,
                            y: nextY,
                        };
                    }
                    return;
                }

                if (!this.structureMap.dragging) {
                    return;
                }

                this.structureMap.panX = this.structureMap.startPanX + (event.clientX - this.structureMap.dragStartX);
                this.structureMap.panY = this.structureMap.startPanY + (event.clientY - this.structureMap.dragStartY);
            },

            endStructureMapDrag() {
                this.structureMap.dragging = false;
                this.structureNodeDrag.active = false;
            },

            startStructureNodeDrag(type, id, event) {
                event.stopPropagation();
                const node = type === 'display'
                    ? this.structureNodes.displays[id]
                    : this.structureNodes[type];

                if (!node) {
                    return;
                }

                this.structureNodeDrag.active = true;
                this.structureNodeDrag.type = type;
                this.structureNodeDrag.id = id;
                this.structureNodeDrag.startX = event.clientX;
                this.structureNodeDrag.startY = event.clientY;
                this.structureNodeDrag.originX = node.x;
                this.structureNodeDrag.originY = node.y;
            },

            structureMapCanvasStyle() {
                return `transform: translate(${this.structureMap.panX}px, ${this.structureMap.panY}px) scale(${this.structureMap.zoom});`;
            },

            structureMapBoardStyle() {
                return `width: ${this.structureNodes.boardWidth}px; height: ${this.structureNodes.boardHeight}px;`;
            },

            structureMapDisplayNodeStyle(index) {
                const displays = this.displayDetail?.structure?.displays || [];
                const node = this.structureNodes.displays[displays[index]?.id];
                if (!node) {
                    return 'left: 0; top: 0; width: 300px;';
                }

                return `left: ${node.x}px; top: ${node.y}px; width: ${node.w}px;`;
            },

            structureMapMainConnectorPath(from, to) {
                const start = this.structureNodes[from];
                const end = this.structureNodes[to];
                if (!start || !end) {
                    return '';
                }

                const startX = start.x + start.w;
                const startY = start.y + (start.h / 2);
                const endX = end.x;
                const endY = end.y + (end.h / 2);
                const delta = Math.max(36, (endX - startX) / 2);

                return `M ${startX} ${startY} C ${startX + delta} ${startY}, ${endX - delta} ${endY}, ${endX} ${endY}`;
            },

            structureMapDisplayConnectorSvg() {
                const workstation = this.structureNodes.workstation;
                const splitter = this.structureNodes.splitter;
                const displays = this.displayDetail?.structure?.displays || [];
                if (!workstation || !splitter || displays.length === 0) {
                    return '';
                }

                const splitEntryX = splitter.x;
                const splitCenterX = splitter.x + splitter.w;
                const splitCenterY = splitter.y + (splitter.h / 2);
                const displayNodes = displays
                    .map((display) => ({ display, node: this.structureNodes.displays[display.id] }))
                    .filter((item) => item.node)
                    .sort((left, right) => {
                        if (left.node.x === right.node.x) {
                            return left.node.y - right.node.y;
                        }

                        return left.node.x - right.node.x;
                    });

                const columns = new Map();
                displayNodes.forEach((entry) => {
                    const key = String(entry.node.x);
                    if (!columns.has(key)) {
                        columns.set(key, []);
                    }
                    columns.get(key).push(entry);
                });

                const splitPath = `M ${workstation.x + workstation.w} ${workstation.y + (workstation.h / 2)} C ${workstation.x + workstation.w + 32} ${workstation.y + (workstation.h / 2)}, ${splitEntryX - 24} ${splitCenterY}, ${splitEntryX} ${splitCenterY}`;

                const columnPaths = Array.from(columns.values()).map((columnEntries, columnIndex) => {
                    const sortedEntries = columnEntries.sort((left, right) => left.node.y - right.node.y);
                    const hubX = Math.min(...sortedEntries.map((entry) => entry.node.x)) - 38;
                    const firstY = sortedEntries[0].node.y + (sortedEntries[0].node.h / 2);
                    const lastY = sortedEntries[sortedEntries.length - 1].node.y + (sortedEntries[sortedEntries.length - 1].node.h / 2);
                    const columnMidY = (firstY + lastY) / 2;
                    const containsActive = sortedEntries.some((entry) => entry.display.active);
                    const travelX = Math.max(90, hubX - splitCenterX);
                    const lead = Math.min(90, Math.max(44, travelX * 0.22));
                    const columnBias = columnIndex * 8;
                    const hubPath = [
                        `M ${splitCenterX} ${splitCenterY}`,
                        `C ${splitCenterX + lead} ${splitCenterY}, ${hubX - 20} ${columnMidY - columnBias}, ${hubX} ${columnMidY}`
                    ].join(' ');

                    const hubStroke = containsActive ? '#0ea5e9' : '#94a3b8';
                    const hubWidth = containsActive ? 2.75 : 2.15;
                    const trunkPath = sortedEntries.length > 1
                        ? `<path d="M ${hubX} ${firstY} L ${hubX} ${lastY}" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" fill="none" />`
                        : '';

                    const leafPaths = sortedEntries.map(({ display, node }, index) => {
                        const endX = node.x;
                        const endY = node.y + (node.h / 2);
                        const fanOffset = ((index - ((sortedEntries.length - 1) / 2)) * 5);
                        const bendX = endX - 34;
                        const path = [
                            `M ${hubX} ${endY + fanOffset}`,
                            `C ${hubX + 16} ${endY + fanOffset}, ${bendX} ${endY}, ${endX} ${endY}`
                        ].join(' ');

                        const stroke = display.active ? '#0ea5e9' : '#94a3b8';
                        const strokeWidth = display.active ? 3.15 : 2.1;
                        return `<path d="${path}" stroke="${stroke}" stroke-width="${strokeWidth}" stroke-linecap="round" stroke-linejoin="round" fill="none" />`;
                    }).join('');

                    return [
                        `<path d="${hubPath}" stroke="${hubStroke}" stroke-width="${hubWidth}" stroke-linecap="round" stroke-linejoin="round" fill="none" />`,
                        trunkPath,
                        leafPaths,
                    ].join('');
                }).join('');

                return [
                    `<path d="${splitPath}" stroke="#94a3b8" stroke-width="2.35" stroke-linecap="round" stroke-linejoin="round" fill="none" />`,
                    columnPaths,
                ].join('');
            },

            structureNodeStyle(type) {
                const node = this.structureNodes[type];
                return `left: ${node.x}px; top: ${node.y}px; width: ${node.w}px; height: ${node.h}px;`;
            },

            confirmSaveDisplaySettings() {
                if (this.savingDisplaySettings) {
                    return;
                }

                this.settingsConfirmOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            closeSettingsSaveConfirm() {
                this.settingsConfirmOpen = false;
                this.$nextTick(() => lucide.createIcons());
            },

            async openDisplayMovePanel() {
                if (!this.displayDetail?.permissions?.move) {
                    return;
                }
                if (!this.current.id) {
                    return;
                }

                this.showDisplayMovePanel = true;
                this.moveLoading = true;

                try {
                    const response = await Perfectlum.request(`/api/display-modal/${this.current.id}/move-options`);
                    this.moveOptions.facilities = this.sortedNamedOptions(response.facilities || []);
                    this.moveOptions.workgroups = this.sortedNamedOptions(response.workgroups || []);
                    this.moveOptions.workstations = this.sortedNamedOptions(response.workstations || []);
                    this.moveForm.facilityId = response.current?.facilityId ? String(response.current.facilityId) : '';
                    this.moveForm.workgroupId = response.current?.workgroupId ? String(response.current.workgroupId) : '';
                    this.moveForm.workstationId = response.current?.workstationId ? String(response.current.workstationId) : '';
                } catch (error) {
                    notify('failed', error.message || 'Failed to load display move options.');
                    this.showDisplayMovePanel = false;
                } finally {
                    this.moveLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            closeDisplayMovePanel() {
                this.showDisplayMovePanel = false;
                this.moveConfirmOpen = false;
                this.moveLoading = false;
                this.moveOptions = {
                    facilities: [],
                    workgroups: [],
                    workstations: [],
                };
                this.moveOptionSearch = {
                    facilities: '',
                    workgroups: '',
                    workstations: '',
                };
                this.moveForm = {
                    facilityId: '',
                    workgroupId: '',
                    workstationId: '',
                };
                this.$nextTick(() => lucide.createIcons());
            },

            confirmDisplayMove() {
                if (!this.moveForm.workstationId || this.moveLoading) {
                    return;
                }

                this.moveConfirmOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },

            closeDisplayMoveConfirm() {
                this.moveConfirmOpen = false;
                this.$nextTick(() => lucide.createIcons());
            },

            selectedMoveFacilityName() {
                return this.findMoveOptionName(this.moveOptions.facilities, this.moveForm.facilityId);
            },

            selectedMoveWorkgroupName() {
                return this.findMoveOptionName(this.moveOptions.workgroups, this.moveForm.workgroupId);
            },

            selectedMoveWorkstationName() {
                return this.findMoveOptionName(this.moveOptions.workstations, this.moveForm.workstationId);
            },

            findMoveOptionName(options, value) {
                const match = (options || []).find((item) => String(item.id) === String(value));
                return match?.name || '-';
            },

            async changeMoveFacility() {
                this.moveForm.workgroupId = '';
                this.moveForm.workstationId = '';
                this.moveConfirmOpen = false;
                this.moveOptions.workstations = [];
                this.moveOptionSearch.workgroups = '';
                this.moveOptionSearch.workstations = '';

                if (!this.moveForm.facilityId) {
                    this.moveOptions.workgroups = [];
                    return;
                }

                this.moveLoading = true;
                try {
                    const workgroups = await Perfectlum.request(`/api/display-modal/workgroups/${this.moveForm.facilityId}`);
                    this.moveOptions.workgroups = this.sortedNamedOptions(workgroups || []);
                } catch (error) {
                    notify('failed', error.message || 'Failed to load workgroups.');
                } finally {
                    this.moveLoading = false;
                }
            },

            async changeMoveWorkgroup() {
                this.moveForm.workstationId = '';
                this.moveConfirmOpen = false;
                this.moveOptionSearch.workstations = '';

                if (!this.moveForm.workgroupId) {
                    this.moveOptions.workstations = [];
                    return;
                }

                this.moveLoading = true;
                try {
                    const workstations = await Perfectlum.request(`/api/display-modal/workstations/${this.moveForm.workgroupId}`);
                    this.moveOptions.workstations = this.sortedNamedOptions(workstations || []);
                } catch (error) {
                    notify('failed', error.message || 'Failed to load workstations.');
                } finally {
                    this.moveLoading = false;
                }
            },

            async moveDisplayHierarchy() {
                if (!this.current.id || !this.moveForm.workstationId) {
                    return;
                }

                this.moveLoading = true;
                const formData = new FormData();
                formData.append('_token', this.csrfToken());
                formData.append('workstation_id', this.moveForm.workstationId);

                try {
                    await Perfectlum.postForm(`/api/display-modal/${this.current.id}/move`, formData);
                    notify('success', 'Display moved successfully.');
                    await this.loadDisplayDetail(this.current.id);
                    this.moveConfirmOpen = false;
                    this.closeDisplayMovePanel();
                } catch (error) {
                    notify('failed', error.message || @js(__('Failed to move display.')));
                } finally {
                    this.moveLoading = false;
                }
            },

            openHistoryReport(item) {
                if (!item || !item.id) {
                    return;
                }

                this.historyReportTitle = item.name || @js(__('History Report'));
                this.historyReportOpen = true;
                this.historyReportLoading = true;
                this.historyReportError = '';
                this.historyReportDetail = null;

                Perfectlum.request(`/api/history-modal/${item.id}`)
                    .then((response) => {
                        this.historyReportDetail = response;
                        this.historyReportTitle = response.name || this.historyReportTitle;
                    })
                    .catch((error) => {
                    this.historyReportError = error.message || @js(__('Failed to load history report.'));
                    })
                    .finally(() => {
                        this.historyReportLoading = false;
                        this.$nextTick(() => lucide.createIcons());
                    });
            },

            closeHistoryReport() {
                this.historyReportOpen = false;
                this.historyReportLoading = false;
                this.historyReportError = '';
                this.historyReportDetail = null;
                this.historyReportTitle = @js(__('History Report'));
            },

            csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            },

            async saveDisplayModal() {
                if (!this.displayDetail?.permissions?.edit) {
                    return;
                }
                if (!this.current.id) {
                    return;
                }

                this.savingDisplaySettings = true;
                this.settingsConfirmOpen = false;
                const formData = new FormData();
                formData.append('_token', this.csrfToken());
                formData.append('exclude', this.displayForm.exclude ? '1' : '0');
                formData.append('CommunicationType', this.displayForm.graphicboardOnly ? '1' : '3');
                formData.append('InternalSensor', this.displayForm.internalSensor ? '1' : '0');
                formData.append('CurrentLUTIndex', this.displayForm.currentLut || '');
                formData.append('InstalationDate', this.displayForm.installationDate || '');
                formData.append('Manufacturer', this.displayForm.manufacturer || '');
                formData.append('Model', this.displayForm.model || '');
                formData.append('SerialNumber', this.displayForm.serial || '');
                formData.append('InventoryNumber', this.displayForm.inventoryNumber || '');
                formData.append('TypeOfDisplay', this.displayForm.typeOfDisplay || '');
                formData.append('DisplayTechnology', this.displayForm.displayTechnology || '');
                formData.append('ScreenSize', this.displayForm.screenSize || '');
                formData.append('ResolutionHorizontal', this.displayForm.resolutionHorizontal || '');
                formData.append('ResolutionVertical', this.displayForm.resolutionVertical || '');
                formData.append('purchase_date', this.financialForm.purchaseDate || '');
                formData.append('initial_value', this.financialForm.initialValue || '');
                formData.append('expected_value', this.financialForm.expectedValue || '');
                formData.append('annual_straight_line', this.financialForm.annualStraightLine || '');
                formData.append('monthly_straight_line', this.financialForm.monthlyStraightLine || '');
                formData.append('current_value', this.financialForm.currentValue || '');
                formData.append('expected_replacement_date', this.financialForm.expectedReplacementDate || '');

                try {
                    await Perfectlum.postForm(`/api/display-modal/${this.current.id}/save`, formData);
                    notify('success', 'Display settings updated.');
                    await this.loadDisplayDetail(this.current.id);
                    this.isEditingDisplaySettings = false;
                } catch (error) {
                    notify('failed', error.message || 'Failed to update display settings.');
                    return;
                } finally {
                    this.savingDisplaySettings = false;
                }
            },

            performanceTrendLayout(timeline) {
                const count = timeline?.length || 0;
                const gap = count > 16 ? 0.8 : 1.6;
                const barWidth = count > 0
                    ? Math.max(1.2, (100 - (Math.max(0, count - 1) * gap)) / count)
                    : 0;

                return { count, gap, barWidth };
            },

            performanceTrendLabelGridStyle(timeline) {
                const count = timeline?.length || 0;
                return `grid-template-columns: repeat(${Math.max(count, 1)}, minmax(0, 1fr));`;
            },

            shouldRenderPerformanceTrendLabel(index, timeline) {
                const count = timeline?.length || 0;
                const labelStep = Math.max(1, Math.ceil(count / 5));
                return index % labelStep === 0 || index === count - 1;
            },

            performanceTrendBucketStyle(index, timeline) {
                const { gap, barWidth } = this.performanceTrendLayout(timeline);
                const left = index * (barWidth + gap);
                return `left:${left}%; width:${barWidth}%;`;
            },

            showPerformanceTrendTooltip(bucket, event) {
                this.performanceTrendTooltip.visible = true;
                this.performanceTrendTooltip.bucket = bucket;
                this.movePerformanceTrendTooltip(bucket, event);
            },

            movePerformanceTrendTooltip(bucket, event) {
                this.performanceTrendTooltip.bucket = bucket;
                const container = event.currentTarget?.parentElement?.parentElement;
                const rect = container?.getBoundingClientRect();
                const localX = rect ? (event.clientX - rect.left + 18) : 18;
                const localY = rect ? (event.clientY - rect.top - 24) : 18;
                const maxX = rect ? Math.max(12, rect.width - 272) : 12;
                const maxY = rect ? Math.max(12, rect.height - 190) : 12;

                this.performanceTrendTooltip.x = Math.max(12, Math.min(maxX, localX));
                this.performanceTrendTooltip.y = Math.max(12, Math.min(maxY, localY));
            },

            hidePerformanceTrendTooltip() {
                this.performanceTrendTooltip.visible = false;
                this.performanceTrendTooltip.bucket = null;
            },

            togglePerformanceTrendBucket(bucket) {
                if (!bucket?.key) {
                    return;
                }

                if (this.selectedPerformanceTrendBucket?.key === bucket.key) {
                    this.selectedPerformanceTrendBucket = null;
                    return;
                }

                this.selectedPerformanceTrendBucket = bucket;
            },

            clearPerformanceTrendBucket() {
                this.selectedPerformanceTrendBucket = null;
            },

            filteredRecentHistory() {
                const recent = this.displayDetail?.history?.recent || [];
                if (!this.selectedPerformanceTrendBucket?.key) {
                    return recent;
                }

                return recent.filter((item) => item.bucketKey === this.selectedPerformanceTrendBucket.key);
            },

            performanceTrendSvg(timeline) {
                if (!timeline || timeline.length === 0) {
                    return '<div class="rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center text-sm text-slate-500">No chart data available.</div>';
                }

                const chartWidth = 100;
                const chartHeight = 66;
                const topPad = 10;
                const bottomPad = 6;
                const drawableHeight = chartHeight - topPad - bottomPad;
                const { count, gap, barWidth } = this.performanceTrendLayout(timeline);
                const maxTotal = Math.max(...timeline.map((bucket) => Number(bucket.total) || 0), 1);

                const gridLines = [0, 25, 50, 75, 100].map((pct) => {
                    const y = topPad + ((100 - pct) / 100) * drawableHeight;
                    return `<line x1="0" y1="${y}" x2="100" y2="${y}" stroke="rgba(148,163,184,0.14)" stroke-width="0.25" />`;
                }).join('');

                const bars = timeline.map((bucket, index) => {
                    const x = index * (barWidth + gap);
                    const total = Number(bucket.total) || 0;
                    const height = total > 0 ? Math.max(4, (total / maxTotal) * drawableHeight) : 2;
                    const y = topPad + (drawableHeight - height);

                    const passedHeight = total > 0 ? (Number(bucket.passed) / total) * height : 0;
                    const failedHeight = total > 0 ? (Number(bucket.failed) / total) * height : 0;
                    const otherHeight = Math.max(0, height - passedHeight - failedHeight);

                    let cursorY = y + height;
                    const segments = [];

                    if (passedHeight > 0) {
                        cursorY -= passedHeight;
                        segments.push(`<rect x="${x}" y="${cursorY}" width="${barWidth}" height="${passedHeight}" rx="0.65" fill="#10b981"><title>${bucket.rangeLabel}: ${bucket.passed} passed</title></rect>`);
                    }
                    if (failedHeight > 0) {
                        cursorY -= failedHeight;
                        segments.push(`<rect x="${x}" y="${cursorY}" width="${barWidth}" height="${failedHeight}" rx="0.65" fill="#f43f5e"><title>${bucket.rangeLabel}: ${bucket.failed} failed</title></rect>`);
                    }
                    if (otherHeight > 0) {
                        cursorY -= otherHeight;
                        segments.push(`<rect x="${x}" y="${cursorY}" width="${barWidth}" height="${otherHeight}" rx="0.65" fill="#f59e0b"><title>${bucket.rangeLabel}: ${bucket.other} skipped/cancelled</title></rect>`);
                    }

                    return segments.join('');
                }).join('');

                const linePoints = timeline.map((bucket, index) => {
                    const x = index * (barWidth + gap) + (barWidth / 2);
                    const y = topPad + ((100 - (Number(bucket.passRate) || 0)) / 100) * drawableHeight;
                    return `${x},${Math.max(topPad, Math.min(topPad + drawableHeight, y))}`;
                }).join(' ');

                const dots = timeline.map((bucket, index) => {
                    const x = index * (barWidth + gap) + (barWidth / 2);
                    const y = topPad + ((100 - (Number(bucket.passRate) || 0)) / 100) * drawableHeight;
                    return `<rect x="${x - 0.42}" y="${y - 0.42}" width="0.84" height="0.84" rx="0.18" fill="#0ea5e9" fill-opacity="0.9"><title>${bucket.rangeLabel}: ${bucket.passRate}% pass rate, ${bucket.total} total runs</title></rect>`;
                }).join('');

                return `
                    <svg viewBox="0 0 100 66" preserveAspectRatio="none" class="h-56 w-full overflow-visible">
                        ${gridLines}
                        ${bars}
                        <polyline fill="none" stroke="#0ea5e9" stroke-opacity="0.8" stroke-width="0.45" stroke-linecap="round" stroke-linejoin="round" points="${linePoints}"></polyline>
                        ${dots}
                    </svg>
                `;
            },

            historyBarClass(tone) {
                if (tone === 'success') return 'bg-emerald-500';
                if (tone === 'danger') return 'bg-rose-500';
                if (tone === 'warning') return 'bg-amber-500';
                return 'bg-slate-400';
            },

            historyBadgeClass(tone) {
                if (tone === 'success') return 'bg-emerald-100 text-emerald-700';
                if (tone === 'danger') return 'bg-rose-100 text-rose-700';
                if (tone === 'warning') return 'bg-amber-100 text-amber-700';
                return 'bg-slate-100 text-slate-600';
            },

            sparklinePoints(points) {
                if (!points || points.length === 0) {
                    return '';
                }

                const values = points.map((point) => Number(point.value) || 0);
                const min = Math.min(...values);
                const max = Math.max(...values);
                const range = max - min || 1;

                return points.map((point, index) => {
                    const x = points.length === 1 ? 50 : (index / (points.length - 1)) * 100;
                    const y = 36 - (((Number(point.value) || 0) - min) / range) * 30;
                    return `${x},${y}`;
                }).join(' ');
            },

            historyChartPoints(points, graph) {
                if (!points || points.length === 0) {
                    return '';
                }

                const xMin = Number(graph?.xMin ?? 0);
                const xMax = Number(graph?.xMax ?? 100);
                const yMin = Number(graph?.yMin ?? 0);
                const yMax = Number(graph?.yMax ?? 100);
                const xRange = xMax - xMin || 1;
                const yRange = yMax - yMin || 1;

                return points.map((point) => {
                    const rawX = Number(point?.x ?? 0);
                    const rawY = Number(point?.y ?? 0);
                    const x = ((rawX - xMin) / xRange) * 100;
                    const y = 52 - (((rawY - yMin) / yRange) * 46);

                    return `${Math.max(0, Math.min(100, x))},${Math.max(4, Math.min(52, y))}`;
                }).join(' ');
            },

            historyGraphSvg(graph) {
                if (!graph || !graph.lines || graph.lines.length === 0) {
                    return '<div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">No chart data available.</div>';
                }

                const lines = graph.lines.map((line) => {
                    const points = this.historyChartPoints(line.points, graph);
                    if (!points) {
                        return '';
                    }

                    const color = line.color || '#0ea5e9';
                    return `<polyline fill="none" stroke="${color}" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round" points="${points}"></polyline>`;
                }).join('');

                return `
                    <svg viewBox="0 0 100 56" preserveAspectRatio="none" class="h-48 w-full overflow-visible">
                        ${lines}
                    </svg>
                `;
            },

            formatMetricValue(value, unit = '') {
                const parsed = Number(value);
                if (Number.isNaN(parsed)) {
                    return '-';
                }

                const formatted = parsed % 1 === 0 ? parsed.toString() : parsed.toFixed(2);
                return unit ? `${formatted} ${unit}` : formatted;
            },
            
            close() {
                this.isOpen = false;
                setTimeout(() => {
                    this.viewStack = [];
                    this.facilityDetail = null;
                    this.facilityError = '';
                    this.facilityLoading = false;
                    this.facilityEditing = false;
                    this.savingFacility = false;
                    this.facilityForm = { name: '', description: '', location: '', timezone: '' };
                    this.displayDetail = null;
                    this.displayError = '';
                    this.displayLoading = false;
                    this.historyLoading = false;
                    this.displayPeriod = 'all';
                    this.workgroupDetail = null;
                    this.workgroupError = '';
                    this.workgroupLoading = false;
                    this.activeWorkgroupTab = 'overview';
                    this.workgroupSettingsEditing = false;
                    this.savingWorkgroupSettings = false;
                    this.workgroupSettingsConfirmOpen = false;
                    this.workgroupOptionSearch = { facilities: '' };
                    this.workgroupSettingsForm = { name: '', address: '', phone: '', facility_id: '' };
                    this.workstationDetail = null;
                    this.workstationError = '';
                    this.workstationLoading = false;
                    this.activeWorkstationTab = 'overview';
                    this.activeWorkstationSettingsTab = 'application';
                    this.workstationSettingsLoading = false;
                    this.workstationSettingsError = '';
                    this.workstationSettingsReady = false;
                    this.workstationSettingsEditing = false;
                    this.savingWorkstationSettings = false;
                    this.workstationSettingsConfirmOpen = false;
                    this.showWorkstationHierarchyEdit = false;
                    this.workstationMoveConfirmOpen = false;
                    this.workstationMoveLoading = false;
                    this.workstationQaClassificationOptions = [];
                    this.workstationSettingsOptions = {};
                    this.workstationOptionSearch = {};
                    this.workstationMoveOptions = { facilities: [], workgroups: [] };
                    this.workstationMoveSearch = { facilities: '', workgroups: '' };
                    this.workstationMoveForm = { facilityId: '', workgroupId: '' };
                    this.workstationSettingsForm = { application: {}, calibration: {}, qa: {}, location: {} };
                    this.isEditingDisplaySettings = false;
                    this.savingDisplaySettings = false;
                    this.settingsConfirmOpen = false;
                    this.displayStructureMapOpen = false;
                    this.workgroupStructureMapOpen = false;
                    this.workstationStructureMapOpen = false;
                    this.structureMapFilter = 'all';
                    this.structureMapExpandedWorkstationId = null;
                    this.structureMapExpandedWorkgroupId = null;
                    this.structureMap = { zoom: 1, panX: 0, panY: 0, dragging: false, dragStartX: 0, dragStartY: 0, startPanX: 0, startPanY: 0 };
                    this.showDisplayMovePanel = false;
                    this.moveConfirmOpen = false;
                    this.moveLoading = false;
                    this.performanceTrendTooltip = { visible: false, x: 0, y: 0, bucket: null };
                    this.selectedPerformanceTrendBucket = null;
                    this.moveOptions = { facilities: [], workgroups: [], workstations: [] };
                    this.moveForm = { facilityId: '', workgroupId: '', workstationId: '' };
                    this.historyReportOpen = false;
                    this.historyReportLoading = false;
                    this.historyReportError = '';
                    this.historyReportDetail = null;
                this.historyReportTitle = @js(__('History Report'));
                }, 300);
            }
        }
    }
</script>
