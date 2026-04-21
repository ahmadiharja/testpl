<!DOCTYPE html>
<html lang="en">
@php
    $timezoneOptions = collect(timezone_identifiers_list())->map(function ($timezone) {
        $offset = now()->setTimezone($timezone)->format('P');
        return [
            'value' => $timezone,
            'label' => sprintf('(UTC%s) %s', $offset, str_replace('_', ' ', $timezone)),
        ];
    })->values();
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('Signup') }} | {{ $settings['Site name'] ?? 'PerfectLum' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .signup-compact-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .signup-compact-input {
            height: 46px;
            width: 100%;
            min-width: 0;
        }
        .signup-stepper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }
        .signup-step-pill {
            flex: 1 1 0;
            height: 6px;
            border-radius: 999px;
            background: rgba(255,255,255,0.1);
            transition: background-color .18s ease;
        }
        .signup-step-pill.active {
            background: #0ea5e9;
        }
        .signup-step-panel {
            min-height: 188px;
        }
        .signup-feedback-slot {
            min-height: 56px;
        }
        .signup-timezone-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            text-align: left;
        }
        .signup-timezone-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 60;
            border: 1px solid rgba(255,255,255,0.1);
            background: #161820;
            border-radius: 16px;
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }
        .signup-timezone-search {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: transparent;
            color: white;
            padding: 14px 16px;
            font-size: 14px;
            outline: none;
        }
        .signup-timezone-list {
            max-height: 220px;
            overflow-y: auto;
            padding: 8px;
        }
        .signup-timezone-item {
            width: 100%;
            text-align: left;
            border: 0;
            background: transparent;
            color: white;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.35;
        }
        .signup-timezone-item:hover,
        .signup-timezone-item.active {
            background: rgba(14, 165, 233, 0.16);
        }
        @media (max-width: 767px) {
            .signup-compact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-[#0f1117] text-white">
    <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-8 lg:flex-row lg:items-center lg:gap-10">
        <div class="hidden flex-1 lg:block">
            <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-[#161820] p-10 shadow-2xl">
                <img src="{{ asset('assets/images/dune_background.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25">
                <div class="absolute inset-0 bg-gradient-to-br from-sky-500/10 via-transparent to-emerald-500/10"></div>
                <div class="relative z-10 flex h-[620px] flex-col justify-between">
                    <img src="{{ url($settings['Site logo'] ?? 'assets/images/perfectlum-logo.png') }}" alt="Site Logo" class="h-10 w-auto">
                    <div>
                        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.28em] text-sky-300">{{ __('Create Account') }}</p>
                        <h1 class="max-w-xl text-4xl font-extrabold leading-tight">{{ __('Register with the same Alpine and Tailwind workflow used by the admin pages.') }}</h1>
                        <p class="mt-4 max-w-lg text-sm text-white/65">{{ __('This replaces the old Bootstrap signup surface and keeps the onboarding stack aligned with the rest of the application.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:max-w-xl" x-data="signupForm()">
            <div class="rounded-[2rem] border border-white/10 bg-[#161820] p-8 shadow-2xl">
                <div class="mb-6">
                    <a href="{{ url('login') }}" class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white">
                        <span>&larr;</span> {{ __('Back to login') }}
                    </a>
                    <h2 class="mt-6 text-3xl font-extrabold tracking-tight">{{ __('Create an account') }}</h2>
                    <p class="mt-2 text-sm text-white/55">{{ __('Provision your initial facility and workgroup in one flow.') }}</p>
                </div>

                <div x-show="!success" class="contents" style="display: none;">
                <div class="signup-stepper" aria-hidden="true">
                    <span class="signup-step-pill" :class="{ 'active': step >= 1 }"></span>
                    <span class="signup-step-pill" :class="{ 'active': step >= 2 }"></span>
                    <span class="signup-step-pill" :class="{ 'active': step >= 3 }"></span>
                </div>
                <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-white/45" x-text="stepLabel"></p>
                </div>

                <div x-show="success" class="mb-4 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-5 text-emerald-100" style="display: none;">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-400/15 text-emerald-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-white">{{ __('Check your email to activate your account') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-emerald-100/90" x-text="successMessage"></p>
                            <p class="mt-2 text-sm text-white/80"><span class="font-medium text-white" x-text="activationEmail"></span></p>
                            <p class="mt-2 text-xs text-white/55">{{ __('If you do not see the message right away, please check your spam or promotions folder.') }}</p>
                        </div>
                    </div>
                    <div class="mt-5 flex items-center gap-3">
                        <a href="{{ url('login') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-400">
                            {{ __('Go to login') }}
                        </a>
                        <button type="button" @click="resetFlow" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/10 bg-transparent px-5 text-sm font-semibold text-white transition hover:bg-white/5">
                            {{ __('Create another account') }}
                        </button>
                    </div>
                </div>

                <form x-show="!success" @submit.prevent="submit" class="space-y-4" style="display: none;">
                    @csrf

                    <div class="signup-step-panel">
                    <div x-show="step === 1" class="signup-compact-grid" style="display: none;">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Name') }}</label>
                            <input x-model="form.fullname" type="text" required class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Email') }}</label>
                            <input x-model="form.email" type="email" required class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Username') }}</label>
                            <input x-model="form.username" @input.debounce.400ms="checkUsername" @blur="checkUsername" type="text" required autocomplete="off" class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div class="md:col-span-2 signup-feedback-slot">
                            <p x-show="emailMessage" x-text="emailMessage" class="mb-2 text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="usernameState === 'taken'" x-text="usernameMessage" class="text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="usernameState === 'available'" x-text="usernameMessage" class="text-xs text-emerald-300" style="display: none;"></p>
                            <p x-show="usernameState === 'checking'" class="text-xs text-white/45" style="display: none;">{{ __('Checking username...') }}</p>
                        </div>
                    </div>

                    <div x-show="step === 2" class="signup-compact-grid" style="display: none;">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Password') }}</label>
                            <input x-model="form.password" type="password" required autocomplete="off" class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Confirm Password') }}</label>
                            <input x-model="form.password_confirmation" type="password" required class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div class="md:col-span-2 signup-feedback-slot">
                            <p x-show="passwordMessage" x-html="passwordMessage" class="text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="!passwordMessage && form.password" class="text-xs text-emerald-300" style="display: none;">{{ __('Password format looks good.') }}</p>
                            <p x-show="confirmationMessage" x-text="confirmationMessage" class="text-xs text-rose-300 mt-2" style="display: none;"></p>
                            <p x-show="!confirmationMessage && form.password && form.password_confirmation" class="text-xs text-emerald-300 mt-2" style="display: none;">{{ __('Passwords match.') }}</p>
                        </div>
                    </div>

                    <div x-show="step === 3" class="signup-compact-grid" style="display: none;">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Facility Name') }}</label>
                            <input x-model="form.facility_name" type="text" required class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Workgroup Name') }}</label>
                            <input x-model="form.workgroup_name" type="text" required class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div class="md:col-span-2 relative" @click.outside="timezoneOpen = false">
                            <label class="mb-2 block text-sm font-medium text-white/70">{{ __('Timezone') }}</label>
                            <button type="button" @click="timezoneOpen = !timezoneOpen; if (timezoneOpen) { $nextTick(() => $refs.timezoneSearch?.focus()) }" class="signup-compact-input rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20 signup-timezone-trigger">
                                <span class="truncate" :class="form.timezone ? 'text-white' : 'text-white/45'" x-text="selectedTimezoneLabel"></span>
                                <svg class="h-4 w-4 shrink-0 text-white/55" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                            <div x-show="timezoneOpen" x-transition class="signup-timezone-menu" style="display: none;">
                                <input x-ref="timezoneSearch" x-model="timezoneSearch" type="text" class="signup-timezone-search" placeholder="{{ __('Search timezone...') }}">
                                <div class="signup-timezone-list">
                                    <template x-for="timezone in filteredTimezones" :key="timezone.value">
                                        <button type="button" class="signup-timezone-item" :class="{ 'active': form.timezone === timezone.value }" @click="selectTimezone(timezone.value)">
                                            <span x-text="timezone.label"></span>
                                        </button>
                                    </template>
                                    <p x-show="filteredTimezones.length === 0" class="px-3 py-2 text-xs text-white/45" style="display: none;">{{ __('No timezones found.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <label x-show="step === 3" class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/70" style="display: none;">
                        <input x-model="form.tos" type="checkbox" class="mt-1 h-4 w-4 rounded border-white/20 bg-[#0f1117] text-sky-500 focus:ring-sky-500/30">
                        <span>{{ __('I agree to the') }} <a href="https://qubyx.com/en/terms-conditions" target="_blank" class="text-sky-300 underline underline-offset-2">{{ __('terms and conditions') }}</a>.</span>
                    </label>

                    <div x-show="error" x-text="error" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300" style="display: none;"></div>
                    <div class="flex items-center gap-3 pt-1">
                        <button type="button" @click="prevStep" x-show="step > 1" class="inline-flex h-12 flex-1 items-center justify-center rounded-xl border border-white/10 bg-transparent px-4 text-sm font-semibold text-white transition hover:bg-white/5" style="display: none;">
                            {{ __('Back') }}
                        </button>
                        <button type="button" @click="nextStep" x-show="step < 3" :disabled="!canAdvanceStep" class="inline-flex h-12 flex-1 items-center justify-center rounded-xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60" style="display: none;">
                            {{ __('Next') }}
                        </button>
                        <button type="submit" x-show="step === 3" :disabled="loading || !canSubmit"
                                class="inline-flex h-12 flex-1 items-center justify-center rounded-xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60" style="display: none;">
                            <span x-text="loading ? @js(__('Please wait...')) : @js(__('Create Account'))"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function signupForm() {
            return {
                form: {
                    fullname: '',
                    email: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    facility_name: '',
                    workgroup_name: '',
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || '',
                    tos: false,
                },
                loading: false,
                error: '',
                success: false,
                successMessage: '',
                activationEmail: '',
                step: 1,
                usernameState: '',
                usernameMessage: '',
                timezoneOptions: @js($timezoneOptions),
                timezoneOpen: false,
                timezoneSearch: '',
                get stepLabel() {
                    return ({
                        1: @js(__('Step 1 - Account details')),
                        2: @js(__('Step 2 - Secure your password')),
                        3: @js(__('Step 3 - Setup facility scope')),
                    })[this.step] || '';
                },
                get passwordMessage() {
                    const password = this.form.password;
                    if (!password) return '';

                    const issues = [];
                    if (password.length < 6) issues.push(@js(__('Password must be at least 6 characters long.')));
                    if (!/[A-Z]/.test(password)) issues.push(@js(__('Password must contain at least one uppercase letter.')));
                    if (!/[a-z]/.test(password)) issues.push(@js(__('Password must contain at least one lowercase letter.')));
                    if (!/[0-9]/.test(password)) issues.push(@js(__('Password must contain at least one digit.')));
                    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) issues.push(@js(__('Password must contain at least one special character.')));
                    return issues.join('<br>');
                },
                get confirmationMessage() {
                    if (!this.form.password_confirmation) return '';
                    return this.form.password === this.form.password_confirmation ? '' : @js(__('Passwords do not match.'));
                },
                get emailMessage() {
                    const email = this.form.email.trim();
                    if (!email) return '';
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
                        ? ''
                        : @js(__('Please enter a valid email address, e.g. example@mail.com.'));
                },
                get canAdvanceStep() {
                    if (this.step === 1) {
                        return !!this.form.fullname.trim() && !!this.form.email.trim() && !this.emailMessage && !!this.form.username.trim() && this.usernameState === 'available';
                    }
                    if (this.step === 2) {
                        return !!this.form.password && !!this.form.password_confirmation && !this.passwordMessage && !this.confirmationMessage;
                    }
                    return false;
                },
                nextStep() {
                    if (this.step < 3 && this.canAdvanceStep) {
                        this.step += 1;
                    }
                },
                prevStep() {
                    if (this.step > 1) {
                        this.step -= 1;
                    }
                },
                get selectedTimezoneLabel() {
                    const match = this.timezoneOptions.find(item => item.value === this.form.timezone);
                    return match ? match.label : @js(__('Select a timezone'));
                },
                get filteredTimezones() {
                    const keyword = this.timezoneSearch.trim().toLowerCase();
                    if (!keyword) {
                        return this.timezoneOptions;
                    }

                    return this.timezoneOptions.filter(item =>
                        item.label.toLowerCase().includes(keyword) ||
                        item.value.toLowerCase().includes(keyword)
                    );
                },
                selectTimezone(value) {
                    this.form.timezone = value;
                    this.timezoneOpen = false;
                    this.timezoneSearch = '';
                },
                async checkUsername() {
                    const username = this.form.username.trim();
                    this.usernameMessage = '';

                    if (!username) {
                        this.usernameState = '';
                        return;
                    }

                    this.usernameState = 'checking';

                    try {
                        const payload = new URLSearchParams({ username });
                        const response = await fetch('{{ url('check-username') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: payload,
                        });
                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.usernameState = 'taken';
                            this.usernameMessage = data.msg || @js(__('Unable to validate username.'));
                            return;
                        }

                        this.usernameState = data.available ? 'available' : 'taken';
                        this.usernameMessage = data.msg || '';
                    } catch (error) {
                        this.usernameState = 'taken';
                        this.usernameMessage = @js(__('Unable to validate username.'));
                    }
                },
                get canSubmit() {
                    return !this.passwordMessage && !this.confirmationMessage && this.form.tos;
                },
                resetFlow() {
                    this.form = {
                        fullname: '',
                        email: '',
                        username: '',
                        password: '',
                        password_confirmation: '',
                        facility_name: '',
                        workgroup_name: '',
                        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || '',
                        tos: false,
                    };
                    this.loading = false;
                    this.error = '';
                    this.success = false;
                    this.successMessage = '';
                    this.activationEmail = '';
                    this.step = 1;
                    this.usernameState = '';
                    this.usernameMessage = '';
                    this.timezoneOpen = false;
                    this.timezoneSearch = '';
                },
                async submit() {
                    this.loading = true;
                    this.error = '';
                    this.success = false;
                    this.successMessage = '';
                    this.activationEmail = '';

                    try {
                        const payload = new URLSearchParams();
                        Object.entries(this.form).forEach(([key, value]) => {
                            payload.append(key, typeof value === 'boolean' ? (value ? '1' : '') : value);
                        });

                        const response = await fetch('{{ url('create-account') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: payload,
                        });
                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.error = data.msg || @js(__('Unable to create account.'));
                            return;
                        }

                        this.success = true;
                        this.successMessage = data.msg || @js(__('Account created successfully. Please check your email and activate your account before signing in.'));
                        this.activationEmail = data.email || this.form.email;
                    } catch (error) {
                        this.error = @js(__('Unable to create account.'));
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</body>
</html>
