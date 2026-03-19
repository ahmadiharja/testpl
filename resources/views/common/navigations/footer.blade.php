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
                
                routes: {
                    'Dashboard': '{{ url("dashboard") }}',
                    'Facilities': '{{ url("facilities-management") }}',
                    'Facility Information': '{{ url("facility-info") }}',
                    'Workgroups': '{{ url("workgroups") }}',
                    'Workstations': '{{ url("workstations") }}',
                    'Displays': '{{ url("displays") }}',
                    'Calibrate Display': '{{ url("display-calibration") }}',
                    'Scheduler': '{{ url("scheduler") }}',
                    'History & Reports': '{{ url("histories-reports") }}',
                    'Site Settings': '{{ url("site-settings") }}',
                    'Application Settings': '{{ url("global-settings") }}',
                    'Alert Settings': '{{ url("alert-settings") }}',
                    'Users': '{{ url("users-management") }}'
                },
                navigate(name) {
                    if (this.routes[name]) {
                        window.location.href = this.routes[name];
                    }
                },
                isActive(name) {
                    return this.activeMenu === name;
                }
            }
        }
    </script>
    
    <!-- Custom Notifications -->
    <div id="notification-center" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3"></div>

    @include('common.modals.hierarchical-location-modal')

    <script>
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

    @stack('scripts')
</body>
</html>
