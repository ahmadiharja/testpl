@php
    $footerRole = $role ?? null;
    $canManageDesktop = in_array($footerRole, ['super', 'admin'], true);
    $canManageUsersDesktop = $canManageDesktop;
    $menuLabels = array_filter([
        'Dashboard' => __('Dashboard'),
        'Facilities' => __('Facilities'),
        'Facility Information' => __('Facility Information'),
        'Workgroups' => __('Workgroups'),
        'Workstations' => __('Workstations'),
        'Displays' => __('Displays'),
        'Calibrate Display' => $canManageDesktop ? __('Calibrate Display') : null,
        'Scheduler' => $canManageDesktop ? __('Scheduler') : null,
        'History & Reports' => __('History & Reports'),
        'Site Settings' => ($role ?? null) === 'super' ? __('Site Settings') : null,
        'Application Settings' => $canManageDesktop ? __('Application Settings') : null,
        'Alert Settings' => $canManageDesktop ? __('Alert Settings') : null,
        'Scope Explorer' => ($role ?? null) === 'super' ? __('Scope Explorer') : null,
        'Client Monitor' => ($role ?? null) === 'super' ? __('Client Monitor') : null,
        'Users' => $canManageUsersDesktop ? __('Users') : null,
    ]);
@endphp

            </div>
        </div>
    </main>

    <!-- App Scripts -->
    <script src="{{url('assets/js/badge-helper.js')}}"></script>

    <!-- Global Application Logic -->
    <script>
        function adminApp() {
            return {
                isMobile: window.innerWidth < 1024,
                sidebarCollapsed: window.innerWidth < 1024,
                activeMenu: (function() {
                    const t = '{{ $title ?? "Dashboard" }}';
                    const map = {
                        'User Management': 'Users',
                        'All Facilities': 'Facilities',
                        'All Workgroups': 'Workgroups',
                        'All Workstations': 'Workstations',
                        'All Displays': 'Displays',
                        'Facility Management': 'Facilities',
                        'Histories & Reports': 'History & Reports',
                        'Histories &amp; Reports': 'History & Reports',
                        'History and Reports': 'History & Reports',
                        'Displays Not Ok': 'Dashboard',
                        'Displays Ok': 'Dashboard',
                        'Search': 'Dashboard',
                        'Schedule Tasks': 'Scheduler',
                        'Global Settings': 'Application Settings',
                        'Display Calibration': 'Calibrate Display'
                    };
                    return map[t] || t;
                })(),
                settingsExpanded: false,
                viewState: 'list', 
                theme: '{{ session("platform", "perfectlum") }}',
                menuLabels: @json($menuLabels),
                
                routes: {
                    'Dashboard': '{{ url("dashboard") }}',
                    'Facilities': '{{ route("facilities.management") }}',
                    'Facility Information': '{{ url("facility-info") }}',
                    'Workgroups': '{{ route("workgroups.management") }}',
                    'Workstations': '{{ route("workstations.management") }}',
                    'Displays': '{{ route("displays.management") }}',
                    @if ($canManageDesktop)
                    'Calibrate Display': '{{ route("displays.calibration") }}',
                    'Scheduler': '{{ route("displays.scheduler") }}',
                    @endif
                    'History & Reports': '{{ route("history.reports") }}',
                    @if (($role ?? null) === 'super')
                    'Site Settings': '{{ url("site-settings") }}',
                    'Scope Explorer': '{{ url("scope-explorer") }}',
                    'Client Monitor': '{{ url("client-monitor") }}',
                    @endif
                    @if ($canManageDesktop)
                    'Application Settings': '{{ url("global-settings") }}',
                    'Alert Settings': '{{ url("alert-settings") }}',
                    @endif
                    @if ($canManageUsersDesktop)
                    'Users': '{{ url("users-management") }}'
                    @endif
                },
                navigate(name) {
                    if (this.routes[name]) {
                        window.location.href = this.routes[name];
                    }
                },
                menuLabel(name) {
                    return this.menuLabels[name] || name;
                },
                isActive(name) {
                    return this.activeMenu === name;
                }
            }
        }
    </script>
    
    <!-- Custom Notifications -->
    <div id="notification-center" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3"></div>

    @include('tasks.schedule_task_modal')
    @include('common.modals.action-confirm-modal')
    <script>
        window.PerfectlumHierarchyModalLoader = window.PerfectlumHierarchyModalLoader || {
            endpoint: @json(url('partials/hierarchy-modal')),
            promise: null,
            hasModal() {
                return !!document.querySelector('[data-hierarchy-modal-root]');
            },
            async load() {
                if (this.hasModal()) {
                    return true;
                }

                if (!this.promise) {
                    this.promise = fetch(this.endpoint, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                        },
                        credentials: 'same-origin',
                    })
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error('Unable to load hierarchy modal.');
                            }
                            return response.text();
                        })
                        .then((html) => {
                            const template = document.createElement('template');
                            template.innerHTML = html.trim();
                            const scripts = Array.from(template.content.querySelectorAll('script'));
                            scripts.forEach((script) => script.remove());
                            document.body.appendChild(template.content);
                            scripts.forEach((script) => {
                                const executable = document.createElement('script');
                                Array.from(script.attributes).forEach((attr) => executable.setAttribute(attr.name, attr.value));
                                executable.textContent = script.textContent;
                                document.body.appendChild(executable);
                            });

                            const root = document.querySelector('[data-hierarchy-modal-root]');
                            if (root && window.Alpine?.initTree) {
                                window.Alpine.initTree(root);
                            }

                            if (window.lucide) {
                                window.lucide.createIcons();
                            }

                            return true;
                        })
                        .catch((error) => {
                            this.promise = null;
                            throw error;
                        });
                }

                return this.promise;
            },
        };

        window.addEventListener('open-hierarchy', (event) => {
            const loader = window.PerfectlumHierarchyModalLoader;
            if (!loader || loader.hasModal()) {
                return;
            }

            const detail = event.detail ? { ...event.detail } : {};
            event.stopImmediatePropagation();

            loader.load()
                .then(() => {
                    window.dispatchEvent(new CustomEvent('open-hierarchy', { detail }));
                })
                .catch(() => {
                    if (typeof window.notify === 'function') {
                        window.notify('failed', 'Unable to open hierarchy detail.');
                    }
                });
        }, true);

        // Modern Notification System
        function notify(type, msg) {
            const container = document.getElementById('notification-center');
            const alertId = 'alert-' + Date.now();
            
            const bgColor = type === 'success' ? 'bg-emerald-500' : 'bg-red-500';
            const icon = type === 'success' ? 'check-circle' : 'alert-circle';
            
            const html = `
                <div id="${alertId}" class="flex items-center gap-4 px-6 py-4 rounded-[1.25rem] text-white shadow-2xl transition-all duration-500 translate-x-12 opacity-0 ${bgColor}">
                    <i data-lucide="${icon}" class="w-5 h-5"></i>
                    <p class="text-[13px] font-bold tracking-wide">${msg}</p>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            lucide.createIcons();
            
            const el = document.getElementById(alertId);
            setTimeout(() => {
                el.classList.remove('translate-x-12', 'opacity-0');
            }, 10);
            
            setTimeout(() => {
                el.classList.add('translate-x-12', 'opacity-0');
                setTimeout(() => el.remove(), 500);
            }, 4000);
        }

        // Field Clipping
        function copy_field(fieldId) {
            const val = document.querySelector(fieldId).value;
            navigator.clipboard.writeText(val).then(() => {
                notify('success', 'Copied to clipboard');
            });
        }

        // Initialize Icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            @if (session('success'))
                notify('success', @json(session('success')));
            @endif

            @if (session('error'))
                notify('error', @json(session('error')));
            @endif
        });
    </script>

    <div id="desktop-page-runtime-scripts" class="hidden" aria-hidden="true">
        <!-- desktop-page-scripts-start -->
        @stack('scripts')
        <!-- desktop-page-scripts-end -->
    </div>
</body>
</html>
