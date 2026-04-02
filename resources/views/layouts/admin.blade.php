<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Qubyx Dashboard' }}</title>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Badge Helper (renderBadge, renderRoleBadge, renderResultBadge) -->
    <script src="{{ url('assets/js/badge-helper.js') }}"></script>

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.3); border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }

        /* Bento Grid Glass Effects */
        .bento-lum {
            background: #ffffff;
            border: 1px solid rgba(240,240,245,0.8);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.03);
        }
        .bento-chroma {
            background: #111216;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);
        }
        
        .bento-row-lum:hover { background-color: #F8F9FA; }
        .bento-row-chroma:hover { background-color: rgba(255,255,255,0.02); }

        .active-pill-lum { background: #1B1B1D; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .active-pill-chroma { background: #fff; color: #1B1B1D; font-weight: 600; box-shadow: 0 4px 12px rgba(255,255,255,0.1); }
    </style>
</head>
<body x-data="adminApp()" 
      @resize.window="isMobile = window.innerWidth < 1024; if(isMobile) sidebarCollapsed = true"
      class="h-screen w-screen overflow-hidden flex transition-colors duration-500"
      data-surface="desktop"
      data-idle-logout-minutes="{{ config('session.idle_timeout', 30) }}"
      data-idle-heartbeat-seconds="{{ config('session.idle_heartbeat_seconds', 60) }}"
      data-idle-heartbeat-url="{{ url('session/heartbeat') }}"
      data-idle-logout-url="{{ url('logout?reason=inactive') }}"
      data-idle-login-url="{{ url('login?surface=desktop') }}"
      :class="theme === 'perfectlum' ? 'bg-[#F4F5F8] text-[#1B1B1D]' : 'bg-[#0A0A0C] text-[#E2E1E6]'">

    {{-- ===================== --}}
    {{-- SIDEBAR --}}
    {{-- ===================== --}}
    @include('admin.partials.sidebar')

    {{-- ===================== --}}
    {{-- MAIN RIGHT AREA --}}
    {{-- ===================== --}}
    <main class="flex-1 flex flex-col h-full">
        
        {{-- TOP HEADER --}}
        @include('admin.partials.header')

        {{-- DASHBOARD SCROLL AREA --}}
        <div class="flex-1 overflow-y-auto px-4 lg:px-8 pb-12 pt-6">
            <div class="max-w-[1600px] mx-auto w-full">

                {{-- PAGE: Dashboard (Bento layout) --}}
                @include('admin.pages.dashboard')

                {{-- PAGE: Generic list (Facilities, Workgroups, Workstations) --}}
                @include('admin.pages.generic-list')

                {{-- PAGE: Displays (full table view) --}}
                @include('admin.pages.displays')

                {{-- PAGE: Scheduler --}}
                @include('admin.pages.scheduler')

                {{-- PAGE: Calibrate Display --}}
                @include('admin.pages.calibrate-display')

                {{-- PAGE: History & Reports --}}
                @include('admin.pages.history-reports')

                {{-- PAGE: Facilities (dedicated table) --}}
                @include('admin.pages.facilities')

                {{-- PAGE: Workgroups (dedicated table) --}}
                @include('admin.pages.workgroups')

                {{-- PAGE: Workstations (dedicated table) --}}
                @include('admin.pages.workstations')

                {{-- PAGE: Detail View (Displays / Workstations) --}}
                @include('admin.pages.detail-view')

                {{-- PAGE: Settings --}}
                @include('admin.pages.settings')

                {{-- PAGE: Users --}}
                @include('admin.pages.users')

            </div>
        </div>

    </main>

    {{-- ===================== --}}
    {{-- MODALS BUILT HERE    --}}
    {{-- ===================== --}}
    @include('admin.modals.due-tasks')
    @include('admin.modals.displays-ok')
    @include('admin.modals.displays-not-ok')
    @include('admin.modals.workstations')
    @include('admin.modals.display-settings')
    @include('admin.modals.workstation-info')
    @include('admin.modals.workgroup-info')
    @include('admin.modals.facility-info')
    @include('admin.modals.report-detail')
    @include('admin.modals.add-user')

    {{-- ===================== --}}
    {{-- ALPINE.JS APP DATA --}}
    {{-- ===================== --}}
    <script>
        function adminApp() {
            return {
                isMobile: window.innerWidth < 1024,
                sidebarCollapsed: window.innerWidth < 1024,
                activeMenu: 'Dashboard',
                viewState: 'list', 
                theme: '{{ $theme ?? "perfectlum" }}',
                settingsTab: 'Site Settings',
                alertSettingsTab: 'Alert Emails',
                showDisplaysOkModal: false,
                showDisplaysNotOkModal: false,
                showDueTasksModal: false,
                showAddUserModal: false,
                showWorkstationsModal: false,
                showWorkstationInfoModal: false,
                workstationViewState: 'info',
                showWorkgroupInfoModal: false,
                workgroupViewState: 'workgroup',
                workgroupModalContext: 'workgroup',
                selectedWorkgroup: { name: '', address: '', phone: '', facility: '' },
                deleteWorkgroupTarget: '',
                workgroupModalContext: 'workgroup',
                showFacilityInfoModal: false,
                facilityViewState: 'facility',
                facilityModalContext: 'facility',
                selectedFacility: { name: '', description: '', location: '', timezone: '' },
                showQuickEditWorkgroup: false,
                quickEditWorkgroup: { name: '', address: '', phone: '', facility: '' },
                showEditWorkstation: false,
                editWorkstation: { name: '', ip: '', mac: '', serial: '', inventory: '', facility: '', workgroup: '' },
                showWorkstationSettings: false,
                workstationSettingsName: null,
                workstationSettingsTab: 'application',
                showDeleteWorkstationConfirm: false,
                deleteWorkstationTarget: '',
                showDisplaySettingsModal: false,
                displaySettingsTab: 'settings',
                selectedDisplay: { name: '', model: '', serial: '' },
                deleteDisplayTarget: '',
                selectedWorkstation: { name: '' },
                displaySettingsBackModal: null,
                historyResultFilter: 'all',
                showReportModal: false,
                selectedReport: null,
                
                usersData: [
                    { first: 'elr_it', last: 'ELR IT', role: 'admin', facility: 'Everlight Radiology', active: true, email: 'elrit@example.com' },
                    { first: 'Andriij', last: 'Andriij', role: 'admin', facility: 'Felenko', active: true, email: 'andriij@example.com' },
                    { first: 'uno', last: 'Uno Kraft', role: 'admin', facility: 'Olorin', active: true, email: 'uno@example.com' },
                    { first: 'olofinabca', last: 'Christoffer Andersson', role: 'admin', facility: 'OlofinAB', active: true, email: 'christoffer@example.com' },
                    { first: 'Juste', last: 'RAN CWEON, KIM fr', role: 'admin', facility: 'Test Facility', active: true, email: 'juste@example.com' },
                    { first: 'llyaCk', last: 'Ilya Porubilyov', role: 'admin', facility: 'qs', active: true, email: 'ilya@example.com' },
                    { first: 'mpretorian', last: 'Mihail Pretorian', role: 'admin', facility: 'Home', active: true, email: 'mihail@example.com' },
                    { first: 'radiitnyu@gmail.com', last: 'Matthew Iglody', role: 'admin', facility: 'NYU Langone', active: true, email: 'radiitnyu@gmail.com' },
                    { first: 'andrewreilly', last: 'Andrew Reilly', role: 'admin', facility: 'Western Health and Social Care Trust', active: true, email: 'andrew@example.com' },
                    { first: 'radperfectlum', last: 'Radperfectlum', role: 'admin', facility: 'uc', active: true, email: 'rad@example.com' }
                ],
                userModalMode: 'add',
                editingUserIndex: null,
                tempUser: { first: '', last: '', username: '', role: '', facility: '', email: '', password: '', active: false },
                
                openAddUserModal() {
                    this.userModalMode = 'add';
                    this.tempUser = { first: '', last: '', username: '', role: '', facility: '', email: '', password: '', active: true };
                    this.showAddUserModal = true;
                },
                
                openEditUserModal(user) {
                    this.userModalMode = 'edit';
                    this.editingUserIndex = this.usersData.indexOf(user);
                    this.tempUser = { ...user, username: user.first };
                    this.showAddUserModal = true;
                },
                
                saveUser() {
                    if (this.userModalMode === 'add') {
                        this.usersData.unshift({ ...this.tempUser });
                    } else if (this.userModalMode === 'edit' && this.editingUserIndex !== null) {
                        this.usersData[this.editingUserIndex] = { ...this.tempUser };
                    }
                    this.showAddUserModal = false;
                },
                
                deleteUser(index) {
                    this.userModalMode = 'delete';
                    this.editingUserIndex = index;
                    this.tempUser = this.usersData[index];
                    this.showAddUserModal = true;
                },
                
                confirmDeleteUser() {
                    if (this.editingUserIndex !== null) {
                        this.usersData.splice(this.editingUserIndex, 1);
                    }
                    this.showAddUserModal = false;
                },

                transitionToModal(callback) {
                    this.showDueTasksModal = false;
                    this.showDisplaysOkModal = false;
                    this.showDisplaysNotOkModal = false;
                    
                    // Allow the DOM to clear the fixed backdrop overlays before rendering the new one
                    setTimeout(() => {
                        callback();
                    }, 400); // 400ms ensures the 300ms leave transitions are completely finished
                },

                openDisplaySettings(display) {
                    this.displaySettingsBackModal = null; // Clear back state
                    this.selectedDisplay = display;
                    this.displaySettingsTab = 'settings';
                    this.showDisplaySettingsModal = true;
                },

                openWorkgroupInfo(workgroup) {
                    this.showWorkstationInfoModal = false;
                    setTimeout(() => {
                        this.selectedWorkgroup = workgroup;
                        this.workgroupViewState = 'workgroup';
                        this.showWorkgroupInfoModal = true;
                    }, 300);
                },

                openFacilityInfo(facility, context = 'facility') {
                    this.showWorkgroupInfoModal = false;
                    this.showWorkstationInfoModal = false;
                    this.facilityModalContext = context;
                    setTimeout(() => {
                        this.selectedFacility = facility;
                        this.facilityViewState = 'facility';
                        this.showFacilityInfoModal = true;
                    }, 300);
                },

                backFromFacilityAction() {
                    if (this.facilityModalContext === 'facilities') {
                        this.showFacilityInfoModal = false;
                        setTimeout(() => { this.facilityModalContext = 'facility'; }, 300);
                    } else {
                        this.facilityViewState = 'facility';
                    }
                },

                openWorkgroupFromFacility(workgroup) {
                    this.openWorkgroupInfo(workgroup);
                },

                openWorkstationFromWorkgroup(workstation) {
                    this.selectedWorkstation = workstation;
                    this.workgroupViewState = 'workstation';
                },

                backFromWorkstationAction() {
                    if (this.workgroupModalContext === 'workstations') {
                        this.showWorkgroupInfoModal = false;
                        setTimeout(() => { this.workgroupModalContext = 'workgroup'; }, 300);
                    } else {
                        this.workgroupViewState = 'workgroup';
                    }
                },

                backFromWorkgroupAction() {
                    if (this.workgroupModalContext === 'workgroups') {
                        this.showWorkgroupInfoModal = false;
                        setTimeout(() => { this.workgroupModalContext = 'workgroup'; }, 300);
                    } else {
                        this.workgroupViewState = 'workgroup';
                    }
                },

                backToWorkgroup() {
                    if (this.workgroupViewState === 'displaySettings') {
                        this.workgroupViewState = 'workstation';
                    } else if (['edit-workgroup', 'delete-workgroup'].includes(this.workgroupViewState)) {
                        this.backFromWorkgroupAction();
                    } else if (['edit-workstation', 'settings-workstation', 'delete-workstation'].includes(this.workgroupViewState)) {
                        this.backFromWorkstationAction();
                    } else {
                        this.workgroupViewState = 'workgroup';
                    }
                },

                openWorkstationInfo(workstation) {
                    this.selectedWorkstation = workstation;
                    this.workstationViewState = 'info';
                    this.showWorkstationInfoModal = true;
                },

                openDisplaySettingsFromWorkstation(display) {
                    this.selectedDisplay = display;
                    this.displaySettingsTab = 'settings';
                    if (this.showWorkgroupInfoModal) {
                        this.workgroupViewState = 'displaySettings';
                    } else {
                        this.workstationViewState = 'displaySettings';
                    }
                },

                backToWorkstationInfo() {
                    if (this.showWorkgroupInfoModal) {
                        this.workgroupViewState = 'workstation';
                    } else {
                        this.workstationViewState = 'info';
                    }
                },

                closeDisplaySettings() {
                    if (this.showDisplaySettingsModal) {
                        this.showDisplaySettingsModal = false;
                    } else if (this.showWorkgroupInfoModal && this.workgroupViewState === 'displaySettings') {
                        this.workgroupViewState = 'workstation';
                    } else if (this.showWorkstationInfoModal && this.workstationViewState === 'displaySettings') {
                        this.workstationViewState = 'info';
                    }
                },

                historyReports: (() => {
                    const mqsaSections = [
                        { name: 'Full Black Test', questions: [
                            { question: 'Is the display surface clean?', answer: 'yes' },
                            { question: 'Are pixel errors visible?', answer: 'yes' },
                        ]},
                        { name: 'Full White Test', questions: [
                            { question: 'Is the display surface clean?', answer: 'yes' },
                            { question: 'Are pixel errors visible?', answer: 'yes' },
                        ]},
                        { name: 'TG18', questions: [
                            { question: 'Is the 5% and the 95% square visible?', answer: 'yes' },
                            { question: 'Can all 16 patches be distinguished?', answer: 'yes' },
                        ]},
                        { name: 'SNR Test', questions: [
                            { question: 'Is the SNR above the threshold?', answer: 'no' },
                            { question: 'Are artifacts visible in the image?', answer: 'yes' },
                        ]},
                    ];
                    const pdm1Sections = [
                        { name: 'Luminance Response', questions: [
                            { question: 'Is the luminance within the acceptable range?', answer: 'yes' },
                            { question: 'Are dark patches distinguishable?', answer: 'yes' },
                        ]},
                        { name: 'Contrast Ratio', questions: [
                            { question: 'Is contrast ratio above 250:1?', answer: 'yes' },
                            { question: 'Is ambient light level acceptable?', answer: 'yes' },
                        ]},
                    ];
                    return [
                        { id: 1,  taskName: 'MQSA Monitor Inspection Acceptance Test', pattern: 'MQSA Monitor Inspection', display: 'LEN LENOVO (LENOVO)',                   workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/04/2026 14:53', result: 'Failed', sections: mqsaSections },
                        { id: 2,  taskName: 'NY PDM1 visual (Monthly)',                pattern: 'NY PDM1',                display: 'Iiyama PL2440HS (11794/10601550)',  workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/04/2026 14:42', result: 'Passed', sections: pdm1Sections },
                        { id: 3,  taskName: 'NY PDM1 visual',                          pattern: 'NY PDM1',                display: 'Iiyama PL2440HS (11794/10601550)',  workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/04/2026 14:42', result: 'Passed', sections: pdm1Sections },
                        { id: 4,  taskName: 'NY PDM1 visual (Monthly)',                pattern: 'NY PDM1',                display: 'LEN LENOVO (LENOVO)',                   workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/04/2026 14:42', result: 'Passed', sections: pdm1Sections },
                        { id: 5,  taskName: 'NY PDM1 visual',                          pattern: 'NY PDM1',                display: 'LEN LENOVO (LENOVO)',                   workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/04/2026 14:39', result: 'Passed', sections: pdm1Sections },
                        { id: 6,  taskName: 'NY PDM1 visual (Monthly)',                pattern: 'NY PDM1',                display: 'Iiyama PL2440HS (11794/10601550)',  workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/03/2026 14:39', result: 'Passed', sections: pdm1Sections },
                        { id: 7,  taskName: 'NY PDM1 visual',                          pattern: 'NY PDM1',                display: 'Iiyama PL2440HS (11794/10601550)',  workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/03/2026 14:38', result: 'Passed', sections: pdm1Sections },
                        { id: 8,  taskName: 'NY PDM1 visual (Monthly)',                pattern: 'NY PDM1',                display: 'LEN LENOVO (LENOVO)',                   workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/03/2026 14:38', result: 'Passed', sections: pdm1Sections },
                        { id: 9,  taskName: 'NY PDM1 visual',                          pattern: 'NY PDM1',                display: 'LEN LENOVO (LENOVO)',                   workstation: 'NEXUS',           workgroup: 'SomeWorkgroup', facility: 'TestFacility', performedAt: '24/03/2026 14:37', result: 'Passed', sections: pdm1Sections },
                        { id: 10, taskName: 'MQSA Monitor Inspection Acceptance Test', pattern: 'MQSA Monitor Inspection', display: 'MSI MSI MAG 275QF (CE2M224706143)', workstation: 'DESKTOP-UR4JCKJ', workgroup: 'Workgroup',     facility: 'Facility',     performedAt: '11/03/2026 16:23', result: 'Failed', sections: mqsaSections },
                    ];
                })(),


                calibrationTasks: [
                    { id: 1, display: 'AUO AUO (AUO)',    workstation: 'LOKE',             workgroup: 'tptworkgroup', facility: 'workfacility', taskType: 'Calibration', scheduleType: 'Start-up', dueDate: '22/01/2026 14:33' },
                    { id: 2, display: 'AUO AUO (AUO)',    workstation: 'LOKE',             workgroup: 'tptworkgroup', facility: 'workfacility', taskType: 'Calibration', scheduleType: 'Start-up', dueDate: '22/01/2026 14:33' },
                    { id: 3, display: 'AUO AUO (AUO)',    workstation: 'LOKE',             workgroup: 'tptworkgroup', facility: 'workfacility', taskType: 'Calibration', scheduleType: 'Start-up', dueDate: '22/01/2026 14:33' },
                    { id: 4, display: '2 (2)',             workstation: 'MBPcuaNguyen2',   workgroup: 'danh',        facility: 'danh',         taskType: 'Calibration', scheduleType: 'Once',     dueDate: '22/01/2026 05:55' },
                    { id: 5, display: 'null (0000001)',    workstation: 'quantums-Mac-mini', workgroup: 'qs',         facility: 'qs',           taskType: 'Calibration', scheduleType: 'Once',     dueDate: '19/12/2025 10:10' },
                    { id: 6, display: '1 (1)',             workstation: 'MBPcuaNguyen2',   workgroup: 'danh',        facility: 'danh',         taskType: 'Calibration', scheduleType: 'Once',     dueDate: '19/12/2025 10:09' },
                    { id: 7, display: '2 (2)',             workstation: 'MBPcuaNguyen2',   workgroup: 'danh',        facility: 'danh',         taskType: 'Calibration', scheduleType: 'Once',     dueDate: '19/12/2025 10:09' },
                    { id: 8, display: '1 (1)',             workstation: 'MBPcuaNguyen2',   workgroup: 'danh',        facility: 'danh',         taskType: 'Calibration', scheduleType: 'Once',     dueDate: '19/12/2025 10:08' },
                    { id: 9, display: '2 (2)',             workstation: 'MBPcuaNguyen2',   workgroup: 'danh',        facility: 'danh',         taskType: 'Calibration', scheduleType: 'Once',     dueDate: '19/12/2025 10:08' },
                ],

                workgroupsData: [
                    { id: 1,  name: 'danh',                       address: 'rg',         phone: '123123',    facility: 'danh' },
                    { id: 2,  name: '350 Euston Road',            address: 'London',     phone: '',          facility: 'Everlight Radiology' },
                    { id: 3,  name: 'Qs-group_test',             address: '',           phone: '',          facility: 'Felenko' },
                    { id: 4,  name: 'Uno hemma',                  address: 'Kungsbacka', phone: '',          facility: 'Test1' },
                    { id: 5,  name: 'ELR Leicester',              address: 'Leicester',  phone: '',          facility: 'Everlight Radiology' },
                    { id: 6,  name: 'ELR Doncaster',             address: 'Doncaster',  phone: '',          facility: 'Everlight Radiology' },
                    { id: 7,  name: 'ELR Warrington',            address: 'Warrington', phone: '',          facility: 'Everlight Radiology' },
                    { id: 8,  name: 'ELR UK Home Rads',          address: '',           phone: '',          facility: 'Everlight Radiology' },
                    { id: 9,  name: 'ELR UK International Home Rads', address: '',      phone: '',          facility: 'Everlight Radiology' },
                    { id: 10, name: 'ELR ANZ Home Rads',         address: '',           phone: '',          facility: 'Everlight Radiology' },
                ],

                facilitiesData: [
                    { id: 1,  name: 'danh',               location: '',       timezone: 'Asia/Bangkok' },
                    { id: 2,  name: 'Everlight Radiology', location: '',       timezone: 'Europe/London' },
                    { id: 3,  name: 'Felenko',             location: '',       timezone: 'America/Noronha' },
                    { id: 4,  name: 'Olorin',              location: '',       timezone: 'Europe/Stockholm' },
                    { id: 5,  name: 'OlorinAB',            location: 'Sweden', timezone: 'Europe/Stockholm' },
                    { id: 6,  name: 'Kostec',              location: '',       timezone: 'Asia/Seoul' },
                    { id: 7,  name: 'Roman',               location: '',       timezone: 'Europe/Helsinki' },
                    { id: 8,  name: 'sis',                 location: '',       timezone: 'Europe/Berlin' },
                    { id: 9,  name: 'Home',                location: '',       timezone: 'Europe/Bucharest' },
                    { id: 10, name: 'NYU Langone',         location: '',       timezone: 'US/Eastern' },
                ],

                workstationsData: [
                    { id: 1,  name: 'ELRUKLONIT48',      workgroup: 'TEST',          facility: 'Everlight Radiology', sleepTime: 'Off' },
                    { id: 2,  name: 'DESKTOP-FEB6J0R',   workgroup: 'Qs-group_test', facility: 'Felenko',            sleepTime: 'Off' },
                    { id: 3,  name: 'UNO-PC',            workgroup: 'Uno hemma',     facility: 'Test1',              sleepTime: 'Off' },
                    { id: 4,  name: 'DESKTOP-PTADGII',   workgroup: 'danh',          facility: 'danh',               sleepTime: 'Off' },
                    { id: 5,  name: 'DESKTOP-FEB6J0R',   workgroup: 'Qs-group_test', facility: 'Felenko',            sleepTime: 'Off' },
                    { id: 6,  name: 'DESKTOP-FEB6J0R',   workgroup: 'Qs-group_test', facility: 'Felenko',            sleepTime: 'Off' },
                    { id: 7,  name: 'UK-HRAD092',        workgroup: 'ELR UK Home Rads', facility: 'Everlight Radiology', sleepTime: 'Off' },
                    { id: 8,  name: 'UK-HRAD093',        workgroup: 'ELR UK Home Rads', facility: 'Everlight Radiology', sleepTime: 'Off' },
                    { id: 9,  name: 'UK-HRAD090',        workgroup: 'ELR UK Home Rads', facility: 'Everlight Radiology', sleepTime: 'Off' },
                    { id: 10, name: 'PCOL1509-001',      workgroup: 'Kungsbacka',    facility: 'OlorinAB',           sleepTime: 'Off' },
                ],

                notOkDisplays: [
                    { id: 1, name: 'Dell U3219Q (9N6WXV2)', location: 'Room 1', error: 'White Level Error: 28.338>10', date: '01/10/2026, 15:35' },
                    { id: 2, name: 'Dell U3219Q (D96WXV2)', location: 'Room 1', error: 'QA Steps Not OK', date: '01/10/2026, 15:35' },
                    { id: 3, name: 'Dell U3223QE (4C3H5P3)', location: 'Room 2', error: 'AAPM Error: 118.514>15', date: '01/10/2026, 15:35' },
                    { id: 4, name: 'LG 21HQ513D (205NTSU2...)', location: 'Room 3', error: 'White Level Error: 14>10', date: '01/10/2026, 15:35' },
                    { id: 5, name: 'Dell U3219Q (G4VVXV2)', location: 'Room 1', error: 'White Level Error: 78.55>10', date: '01/10/2026, 15:35' },
                    { id: 6, name: 'null (212KCNLKE991)', location: 'Unknown', error: 'White Level Error: 35.2>10', date: '01/10/2026, 15:34' }
                ],

                displaysOkData: [
                    { id: 1, name: 'Dell DELL UP2716D (KRXTR66F083L)', inventory: '', workstation: 'ELRUKLONIT48', workgroup: 'TEST', facility: 'Everlight Radiology', status: 'OK' },
                    { id: 2, name: 'Dell DELL P2419H (2K3JPM2)', inventory: '', workstation: 'ELRUKLONIT48', workgroup: 'TEST', facility: 'Everlight Radiology', status: 'OK' },
                    { id: 3, name: 'Dell DELL UP2414Q (6X55C37O044L)', inventory: '', workstation: 'DESKTOP-FEB6J0R', workgroup: 'Qs-group_test', facility: 'Felenko', status: 'OK' },
                    { id: 4, name: 'AUO AUO (AUO)', inventory: '', workstation: 'UNO-PC', workgroup: 'Uno hemma', facility: 'Test1', status: 'OK' },
                    { id: 5, name: 'DVA Olorin MC221D (OLR170M0072)', inventory: '', workstation: 'UNO-PC', workgroup: 'Uno hemma', facility: 'Test1', status: 'OK' },
                    { id: 6, name: 'CMN 14d9 (00000000)', inventory: '', workstation: 'DESKTOP-PTADGII', workgroup: 'danh', facility: 'danh', status: 'OK' },
                    { id: 7, name: 'Dell DELL UP2414Q (6X55C37O044L)', inventory: '', workstation: 'DESKTOP-FEB6J0R', workgroup: 'Qs-group_test', facility: 'Felenko', status: 'OK' },
                    { id: 8, name: 'Dell DELL UP2414Q (6X55C37O044L)', inventory: '', workstation: 'DESKTOP-FEB6J0R', workgroup: 'Qs-group_test', facility: 'Felenko', status: 'OK' },
                    { id: 9, name: 'Dell DELL UP2414Q (6X55C41P219L)', inventory: '', workstation: 'DESKTOP-FEB6J0R', workgroup: 'Qs-group_test', facility: 'Felenko', status: 'OK' },
                    { id: 10, name: 'DVA MCD270D (OLS52120004)', inventory: '', workstation: 'UNO-PC', workgroup: 'Uno hemma', facility: 'Test1', status: 'OK' }
                ],

                displaysNotOkData: [
                    { id: 11, name: 'Dell U3219Q (9N6WXV2)', inventory: '13-A33', workstation: 'ROOM-1-PC', workgroup: 'Triage', facility: 'Main Hospital', error: 'White Level Error: 28.338>10', status: 'Error' },
                    { id: 12, name: 'Dell U3219Q (D96WXV2)', inventory: '13-A34', workstation: 'ROOM-1-PC', workgroup: 'Triage', facility: 'Main Hospital', error: 'QA Steps Not OK', status: 'Error' },
                    { id: 13, name: 'Dell U3223QE (4C3H5P3)', inventory: '22-B12', workstation: 'ROOM-2-PC', workgroup: 'Triage', facility: 'Main Hospital', error: 'AAPM Error: 118.514>15', status: 'Error' },
                    { id: 14, name: 'LG 21HQ513D (205NTSU2...)', inventory: '55-C11', workstation: 'ROOM-3-MAC', workgroup: 'Radiology', facility: 'City Clinic', error: 'White Level Error: 14>10', status: 'Error' },
                    { id: 15, name: 'Dell U3219Q (G4VVXV2)', inventory: '13-A40', workstation: 'ROOM-1-PC', workgroup: 'Triage', facility: 'Main Hospital', error: 'White Level Error: 78.55>10', status: 'Error' },
                    { id: 16, name: 'null (212KCNLKE991)', inventory: 'Unknown', workstation: 'Unknown-PC', workgroup: 'Unknown', facility: 'Unknown', error: 'White Level Error: 35.2>10', status: 'Error' }
                ],

                get allDisplaysData() {
                    return [...this.displaysNotOkData, ...this.displaysOkData];
                },

                dueTasks: [
                    { id: 1, name: 'Calibration', display: 'LG 21HK512D-B (RD180)', workstation: 'DESKTOP-FEB6J0R', workgroup: 'Qs-group test', type: 'Calibration', schedule: 'Start-up', date: '12/03/2026 11:52' },
                    { id: 2, name: 'Calibration', display: 'Dell UP2414Q', workstation: 'DESKTOP-FEB6J0R', workgroup: 'Qs-group test', type: 'Calibration', schedule: 'Start-up', date: '12/03/2026 11:52' },
                    { id: 3, name: 'Calibration', display: 'LEN B140QAN01.5', workstation: 'RADO', workgroup: 'Sofia', type: 'Calibration', schedule: 'Start-up', date: '10/04/2026 13:36' },
                    { id: 4, name: 'Calibration', display: 'LG BK550Y', workstation: 'DESKTOP-O1VIORD', workgroup: 'Sunset Park Rad', type: 'Calibration', schedule: 'Start-up', date: '06/04/2026 19:22' },
                    { id: 5, name: 'Calibration', display: 'LGD LG Display', workstation: 'DESKTOP-3N2RlQ2', workgroup: 'twin 16F', type: 'Calibration', schedule: 'Start-up', date: '21/02/2026 06:26' },
                    { id: 6, name: 'Calibration', display: 'LG 31HN713D', workstation: 'OD Gram4', workgroup: 'SE Lab', type: 'Calibration', schedule: 'Start-up', date: '23/03/2026 15:21' },
                    { id: 7, name: 'Calibration', display: 'Dell P2213', workstation: 'FRG-D91M6382', workgroup: 'Mammo', type: 'Calibration', schedule: 'Start-up', date: '29/03/2026 14:44' }
                ],

                schedulerTasks: [
                    { id: 1, display: 'iiyama PL2440HS (11794106015...)', workstation: 'NEXUS', workgroup: 'SomeWorkgroup', facility: 'TestFacility', type: 'NY PDM1 Quarter (measurement)', scheduleType: 'Quarterly', dueDate: '24/07/2026 04:00' },
                    { id: 2, display: 'LEN LENOVO (LENOVO)', workstation: 'NEXUS', workgroup: 'SomeWorkgroup', facility: 'TestFacility', type: 'NY PDM1 Quarter (measurement)', scheduleType: 'Quarterly', dueDate: '24/07/2026 02:00' },
                    { id: 3, display: 'iiyama PL2440HS (11794106015...)', workstation: 'NEXUS', workgroup: 'SomeWorkgroup', facility: 'TestFacility', type: 'NY PDM1 Month (visual)', scheduleType: 'Monthly', dueDate: '24/05/2026 04:00' },
                    { id: 4, display: 'LEN LENOVO (LENOVO)', workstation: 'NEXUS', workgroup: 'SomeWorkgroup', facility: 'TestFacility', type: 'NY PDM1 Month (visual)', scheduleType: 'Monthly', dueDate: '24/05/2026 02:00' },
                    { id: 5, display: 'iiyama PL2440HS (11794106015...)', workstation: 'NEXUS', workgroup: 'SomeWorkgroup', facility: 'TestFacility', type: 'NY PDM1 Twice a week (visual)', scheduleType: 'Twice a week', dueDate: '28/04/2026 04:00' },
                    { id: 6, display: 'LEN LENOVO (LENOVO)', workstation: 'NEXUS', workgroup: 'SomeWorkgroup', facility: 'TestFacility', type: 'NY PDM1 Twice a week (visual)', scheduleType: 'Twice a week', dueDate: '28/04/2026 02:00' },
                    { id: 7, display: 'MSI MSI MAG 27CQF (CET2H24706143)', workstation: 'DESKTOP-D7SQSF6', workgroup: 'Workgroupname', facility: 'Facility', type: 'MQSA Monitor Inspection Twice a week', scheduleType: 'Twice a week', dueDate: '11/03/2026 02:00' },
                    { id: 8, display: 'LG 21HQ513D (300NTDVA147...)', workstation: 'HEX-RWS205', workgroup: 'Hexarad', facility: 'Everest', type: 'AAPM TG18 Annual (visual)', scheduleType: 'Annually', dueDate: '24/01/2026 17:07' },
                ],

                get upcomingTasks() {
                    return this.schedulerTasks;
                },

                history: [
                    { id: 1, action: 'Calibration (Target 400)', target: 'Eizo Radiforce', date: 'Today, 14:30', status: 'Passed' },
                    { id: 2, action: 'Visual QA test', target: 'WS-Mammo-03', date: 'Yesterday, 09:15', status: 'Failed' },
                    { id: 3, action: 'Conformance Check', target: 'Barco Coronis 5MP', date: '2026-01-15', status: 'Passed' },
                    { id: 4, action: 'Luminance Test', target: 'LG 21HK512D', date: '2026-01-14', status: 'Passed' },
                    { id: 5, action: 'DICOM Verification', target: 'Dell UP2414Q', date: '2026-01-12', status: 'Passed' }
                ],

                navigate(menu) {
                    this.activeMenu = menu;
                    this.viewState = 'list';
                    if (this.isMobile) this.sidebarCollapsed = true;
                },

                openDetail(type) {
                    this.activeMenu = type;
                    this.viewState = 'detail';
                },

                markAsOk(index) {
                    this.notOkDisplays.splice(index, 1);
                },

                openReport(id) {
                    alert("Opening full PDF report viewer module...");
                },
                
                init() {
                    this.$watch('activeMenu', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('viewState', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('settingsTab', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('showDisplaysOkModal', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('showDisplaysNotOkModal', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('showDueTasksModal', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('showWorkstationsModal', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('showWorkstationInfoModal', () => setTimeout(() => lucide.createIcons(), 10));
                    this.$watch('showWorkgroupInfoModal', () => setTimeout(() => lucide.createIcons(), 10));
                }
            }
        }

        document.addEventListener('alpine:init', () => { setTimeout(() => { lucide.createIcons(); }, 10); });
    </script>
</body>
</html>
