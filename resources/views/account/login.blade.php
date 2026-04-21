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
    <title>{{ __($title ?? 'Login') }} | {{ $settings['Site name'] ?? 'PerfectLum' }}</title>
    <link rel="icon" href="{{ asset('assets/images/perfectlum_circle.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/perfectlum_circle.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/perfectlum_circle.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #4A4A58; /* Exterior background similar to the shot */
        }
        
        .modal-bg {
            background-color: #262534;
        }

        .input-bg {
            background-color: #353444;
            border-color: #494858;
        }

        .input-bg:focus {
            border-color: #7C5CBF;
            outline: none;
            box-shadow: 0 0 0 1px #7C5CBF;
        }

        .btn-primary {
            background-color: #7C5CBF;
        }
        
        .btn-primary:hover {
            background-color: #6C50A6;
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid #494858;
            color: #E2E1E6;
        }

        .btn-secondary:hover {
            background-color: #353444;
        }

        .text-muted {
            color: #A19FAD;
        }
        
        .text-accent {
            color: #7C5CBF;
        }
        
        .divider-line {
            background-color: #494858;
            height: 1px;
            flex-grow: 1;
        }

        /* Checkbox customization */
        .custom-checkbox {
            appearance: none;
            background-color: #E2E1E6;
            margin: 0;
            cursor: pointer;
            border-radius: 4px;
            display: grid;
            place-content: center;
        }
        
        .custom-checkbox::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em #262534;
            transform-origin: center;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }
        
        .custom-checkbox:checked::before {
            transform: scale(1);
        }

        /* Hide scrollbar for clean look */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #494858;
            border-radius: 10px;
        }

        .auth-compact-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
        }

        .auth-compact-input {
            width: 100%;
            min-width: 0;
            font-size: 14px;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .auth-stepper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .auth-step-pill {
            flex: 1 1 0;
            height: 6px;
            border-radius: 999px;
            background: rgba(255,255,255,0.1);
            transition: background-color .18s ease;
        }

        .auth-step-pill.active {
            background: #7C5CBF;
        }

        .auth-step-panel {
            min-height: 180px;
        }

        .auth-feedback-slot {
            min-height: 54px;
        }

        .auth-timezone-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            text-align: left;
        }

        .auth-timezone-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 60;
            border: 1px solid #494858;
            background: #2e2d3d;
            border-radius: 16px;
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        .auth-timezone-search {
            width: 100%;
            border: 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: transparent;
            color: white;
            padding: 14px 16px;
            font-size: 14px;
            outline: none;
        }

        .auth-timezone-list {
            max-height: 220px;
            overflow-y: auto;
            padding: 8px;
        }

        .auth-timezone-item {
            width: 100%;
            text-align: left;
            border: 0;
            background: transparent;
            color: #E2E1E6;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.35;
        }

        .auth-timezone-item:hover,
        .auth-timezone-item.active {
            background: rgba(124, 92, 191, 0.18);
        }

    </style>
</head>
<body x-data="authSystem({ initialMode: @js($authMode ?? 'login') })" class="min-h-screen flex items-center justify-center p-4 text-[#E2E1E6]">

    <!-- Main Modal Container -->
    <div class="modal-bg w-full max-w-[1100px] min-h-[700px] rounded-3xl shadow-2xl flex flex-col md:flex-row overflow-hidden relative">
        
        <!-- Left Image Section (Responsive: hidden on small screens or stacked) -->
        <div class="hidden md:block md:w-1/2 p-3 relative">
            <div class="absolute inset-0 bg-cover bg-center rounded-2xl m-3 overflow-hidden shadow-inner" 
                 style="background-image: url('{{ asset('assets/images/dune_background.png') }}'); background-position: center bottom;">
                
                <!-- Overlay gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-black/30"></div>

                <!-- Top Header Overlay -->
                <div class="absolute top-0 left-0 right-0 p-8 flex justify-between items-center z-10 w-full">
                    <!-- PerfectLum Logo -->
                    <div class="flex items-center">
                        <img src="{{ asset('assets/images/perfectlum-logo.png') }}" alt="PerfectLum" class="h-8 w-auto">
                    </div>

                    <a href="https://qubyx.com" target="_blank" rel="noopener noreferrer" class="text-xs bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 transition-colors text-white py-2 px-4 rounded-full flex items-center gap-1 backdrop-filter">
                        {{ __('Back to qubyx.com') }} <span class="text-[10px]">&rarr;</span>
                    </a>
                </div>

                <!-- Bottom Text Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-12 z-10">
                    <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.35em] text-white/70">Remote Operations Platform</p>
                    <h2 class="max-w-md text-3xl font-medium leading-tight text-white drop-shadow-md">See every display. Act before issues spread.</h2>
                    <p class="mt-4 max-w-md text-sm leading-6 text-white/80">
                        PerfectLum centralizes display health, scheduled QA, and workstation sync across distributed facilities.
                    </p>
                </div>

            </div>
        </div>

        <!-- Right Form Section -->
        <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col relative overflow-y-auto" :class="mode === 'register' && !registerSuccess ? 'justify-start md:pt-14' : 'justify-center'">
            
            <!-- Logo for mobile (hidden on desktop) -->
            <div class="md:hidden flex items-center mb-8">
                <img src="{{ asset('assets/images/perfectlum-logo.png') }}" alt="PerfectLum" class="h-8 w-auto">
            </div>

            <!-- ======================= LOGIN VIEW ======================= -->
            <div x-show="mode === 'login'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h1 class="text-4xl font-semibold text-white mb-2">{{ __('Sign in to PerfectLum') }}</h1>
                <p class="text-muted text-sm mb-3">{{ __('Access the remote calibration workspace for display health, task scheduling, and sync activity.') }}</p>
                <p class="text-muted text-sm mb-10">
                    {{ __("Need a workspace account?") }}
                    <a href="{{ url('signup') }}" class="text-[#E2E1E6] hover:text-white underline decoration-[#8a8899] underline-offset-2 transition-colors">{{ __('Sign up') }}</a>
                </p>

                @if (session('idle_logout_notice'))
                    <div class="mb-5 rounded-xl border border-amber-400/40 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">
                        {{ session('idle_logout_notice') }}
                    </div>
                @endif

                <form method="POST" action="{{ url('login') }}" class="space-y-5" @submit.prevent="checkLogin">
                    @csrf
                    
                    <div>
                        <input name="email" type="text" placeholder="{{ __('Email / Username') }}" class="w-full input-bg border text-sm rounded-lg px-4 py-3.5 text-white placeholder-[#8a8899] transition-all" required autofocus>
                    </div>

                    <div class="relative" x-data="{ showPassword: false }">
                        <input name="password" :type="showPassword ? 'text' : 'password'" placeholder="{{ __('Enter your password') }}" class="w-full input-bg border text-sm rounded-lg pl-4 pr-12 py-3.5 text-white placeholder-[#8a8899] transition-all" required>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-muted hover:text-white transition-colors">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <svg x-show="showPassword" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path></svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer group select-none">
                            <input name="remember" value="1" type="checkbox" class="custom-checkbox w-4 h-4 shrink-0 rounded appearance-none group-hover:ring-2 ring-white/20 transition-all">
                            <span class="text-sm font-medium leading-none text-muted">{{ __('Remember me') }}</span>
                        </label>
                        <a href="{{ url('forgot-password') }}" class="text-sm text-muted hover:text-white transition-colors underline decoration-transparent hover:decoration-[#8a8899] underline-offset-2">{{ __('Forgot password?') }}</a>
                    </div>
                    
                    <!-- Alert Messages Area -->
                    <div id="login_error" class="hidden bg-red-500/10 border border-red-500/50 text-red-500 text-sm rounded-lg p-3 mt-2"></div>
                    <div id="login_success" class="hidden bg-green-500/10 border border-green-500/50 text-green-500 text-sm rounded-lg p-3 mt-2"></div>

                    <button id="submit_btn" type="submit" class="btn-primary w-full text-white font-medium text-sm rounded-lg py-3.5 mt-2 transition-transform active:scale-[0.98]">
                        {{ __('Log in') }}
                    </button>
                    
                </form>


            </div>

            <!-- ======================= REGISTER VIEW ======================= -->
            <div x-show="mode === 'register'" x-transition:enter="transition ease-out duration-300 transform delay-150" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div x-show="!registerSuccess" class="contents" style="display: none;">
                    <h1 class="text-4xl font-semibold text-white mb-2 tracking-tight">{{ __('Create a PerfectLum account') }}</h1>
                    <p class="text-muted text-sm mb-3">
                        {{ __('Set up access to the remote calibration workspace.') }}
                    </p>
                    <p class="text-muted text-sm mb-6">
                        {{ __('Already have an account?') }}
                        <a href="{{ url('login') }}" class="text-[#E2E1E6] hover:text-white underline decoration-[#8a8899] underline-offset-2 transition-colors">{{ __('Log in') }}</a>
                    </p>
                    <div class="auth-stepper" aria-hidden="true">
                        <span class="auth-step-pill" :class="{ 'active': registerStep >= 1 }"></span>
                        <span class="auth-step-pill" :class="{ 'active': registerStep >= 2 }"></span>
                        <span class="auth-step-pill" :class="{ 'active': registerStep >= 3 }"></span>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/45 mb-4" x-text="registerStepLabel"></p>
                </div>

                <div x-show="registerSuccess" class="text-white" style="display: none;">
                    <div class="flex flex-col items-start">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-emerald-300/45 bg-emerald-400/10 text-emerald-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="mt-5 text-xs font-bold uppercase tracking-[0.26em] text-emerald-300/85">{{ __('Account Created') }}</p>
                        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white">{{ __('Check your email') }}</h1>
                        <p class="mt-3 max-w-md text-sm leading-6 text-white/65" x-text="registerSuccessMessage"></p>
                        <div class="mt-4 inline-flex max-w-full items-center gap-2 rounded-full border border-white/10 bg-white/[0.045] px-4 py-2 text-sm font-semibold text-white">
                            <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-300"></span>
                            <span class="truncate" x-text="registerActivationEmail"></span>
                        </div>
                        <p class="mt-3 max-w-md text-xs leading-5 text-white/45">{{ __('If the activation email is not in your inbox, please check your spam or promotions folder.') }}</p>
                    </div>
                    <button type="button" @click="backToLoginAfterRegister" class="btn-primary mt-6 inline-flex h-10 items-center justify-center rounded-lg px-5 text-sm font-semibold text-white">
                        {{ __('Go to login') }}
                    </button>
                </div>

                <form x-show="!registerSuccess" @submit.prevent="submitRegister" class="space-y-3" autocomplete="off" style="display: none;">
                    <div class="auth-step-panel">
                    <div x-show="registerStep === 1" class="auth-compact-grid" style="display: none;">
                        <div>
                            <input x-model="register.fullname" type="text" placeholder="{{ __('Name') }}" class="auth-compact-input input-bg border text-white placeholder-[#8a8899] transition-all" required autocomplete="off" autofocus>
                        </div>
                        <div>
                            <input x-model="register.email" @input.debounce.400ms="checkRegisterEmail" @blur="checkRegisterEmail" type="email" placeholder="{{ __('Email') }}" class="auth-compact-input input-bg border text-white placeholder-[#8a8899] transition-all" required autocomplete="off">
                        </div>
                        <div>
                            <input x-model="register.username" @input.debounce.400ms="checkRegisterUsername" @blur="checkRegisterUsername" type="text" placeholder="{{ __('Username') }}" class="auth-compact-input input-bg border text-white placeholder-[#8a8899] transition-all" required autocomplete="off">
                        </div>
                        <div class="auth-feedback-slot">
                            <p x-show="registerEmailMessage" x-text="registerEmailMessage" class="mb-2 text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="!registerEmailMessage && registerEmailState === 'taken'" x-text="registerEmailStateMessage" class="mb-2 text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="!registerEmailMessage && registerEmailState === 'available'" x-text="registerEmailStateMessage" class="mb-2 text-xs text-emerald-300" style="display: none;"></p>
                            <p x-show="!registerEmailMessage && registerEmailState === 'checking'" class="mb-2 text-xs text-white/45" style="display: none;">{{ __('Checking email...') }}</p>
                            <p x-show="registerUsernameState === 'taken'" x-text="registerUsernameMessage" class="text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="registerUsernameState === 'available'" x-text="registerUsernameMessage" class="text-xs text-emerald-300" style="display: none;"></p>
                            <p x-show="registerUsernameState === 'checking'" class="text-xs text-white/45" style="display: none;">{{ __('Checking username...') }}</p>
                        </div>
                    </div>

                    <div x-show="registerStep === 2" class="auth-compact-grid" style="display: none;">
                        <div class="relative" x-data="{ showRegisterPassword: false }">
                            <input x-model="register.password" :type="showRegisterPassword ? 'text' : 'password'" placeholder="{{ __('Password') }}" class="auth-compact-input input-bg border pr-12 text-white placeholder-[#8a8899] transition-all" required autocomplete="new-password">
                            <button type="button" @click="showRegisterPassword = !showRegisterPassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-muted hover:text-white transition-colors">
                                <svg x-show="!showRegisterPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <svg x-show="showRegisterPassword" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path></svg>
                            </button>
                        </div>
                        <div class="relative" x-data="{ showRegisterConfirmPassword: false }">
                            <input x-model="register.password_confirmation" :type="showRegisterConfirmPassword ? 'text' : 'password'" placeholder="{{ __('Confirm Password') }}" class="auth-compact-input input-bg border pr-12 text-white placeholder-[#8a8899] transition-all" required autocomplete="new-password">
                            <button type="button" @click="showRegisterConfirmPassword = !showRegisterConfirmPassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-muted hover:text-white transition-colors">
                                <svg x-show="!showRegisterConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <svg x-show="showRegisterConfirmPassword" class="w-5 h-5" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path></svg>
                            </button>
                        </div>
                        <div class="auth-feedback-slot">
                            <p x-show="registerPasswordMessage" x-html="registerPasswordMessage" class="text-xs text-rose-300" style="display: none;"></p>
                            <p x-show="!registerPasswordMessage && register.password" class="text-xs text-emerald-300" style="display: none;">{{ __('Password format looks good.') }}</p>
                            <p x-show="registerConfirmationMessage" x-text="registerConfirmationMessage" class="text-xs text-rose-300 mt-2" style="display: none;"></p>
                            <p x-show="!registerConfirmationMessage && register.password && register.password_confirmation" class="text-xs text-emerald-300 mt-2" style="display: none;">{{ __('Passwords match.') }}</p>
                        </div>
                    </div>

                    <div x-show="registerStep === 3" class="auth-compact-grid" style="display: none;">
                        <div>
                            <input x-model="register.facility_name" type="text" placeholder="{{ __('Facility Name') }}" class="auth-compact-input input-bg border text-white placeholder-[#8a8899] transition-all" required autocomplete="off">
                        </div>
                        <div>
                            <input x-model="register.workgroup_name" type="text" placeholder="{{ __('Workgroup Name') }}" class="auth-compact-input input-bg border text-white placeholder-[#8a8899] transition-all" required autocomplete="off">
                        </div>
                        <div class="relative" @click.outside="registerTimezoneOpen = false">
                            <button type="button" @click="registerTimezoneOpen = !registerTimezoneOpen; if (registerTimezoneOpen) { $nextTick(() => $refs.registerTimezoneSearch?.focus()) }" class="auth-compact-input input-bg border auth-timezone-trigger text-white transition-all">
                                <span class="truncate" :class="register.timezone ? 'text-white' : 'text-[#8a8899]'" x-text="selectedRegisterTimezoneLabel"></span>
                                <svg class="h-4 w-4 shrink-0 text-[#b8b4c7]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                            <div x-show="registerTimezoneOpen" x-transition class="auth-timezone-menu" style="display: none;">
                                <input x-ref="registerTimezoneSearch" x-model="registerTimezoneSearch" type="text" class="auth-timezone-search" placeholder="{{ __('Search timezone...') }}">
                                <div class="auth-timezone-list">
                                    <template x-for="timezone in filteredRegisterTimezones" :key="timezone.value">
                                        <button type="button" class="auth-timezone-item" :class="{ 'active': register.timezone === timezone.value }" @click="selectRegisterTimezone(timezone.value)">
                                            <span x-text="timezone.label"></span>
                                        </button>
                                    </template>
                                    <p x-show="filteredRegisterTimezones.length === 0" class="px-3 py-2 text-xs text-white/45" style="display: none;">{{ __('No timezones found.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div x-show="registerStep === 3" class="flex items-start mt-3 mb-1" style="display: none;">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer group select-none mt-1">
                            <input x-model="register.tos" type="checkbox" class="custom-checkbox shrink-0 w-4 h-4 rounded appearance-none group-hover:ring-2 ring-white/20 transition-all">
                            <span class="text-sm font-medium leading-relaxed text-muted">{{ __('I agree to the') }} <a href="https://qubyx.com/en/terms-conditions" target="_blank" rel="noopener noreferrer" class="underline decoration-[#8a8899] hover:text-white transition-colors">{{ __('Terms & Conditions') }}</a></span>
                        </label>
                    </div>

                    <div x-show="registerError" x-text="registerError" class="rounded-lg border border-red-500/50 bg-red-500/10 p-3 text-sm text-red-300" style="display: none;"></div>
                    <div class="flex items-center justify-between gap-3 pt-2">
                        <button type="button" @click="prevRegisterStep" x-show="registerStep > 1" class="btn-secondary flex-1 rounded-lg py-3 text-sm font-medium" style="display: none;">
                            {{ __('Back') }}
                        </button>
                        <button type="button" @click="nextRegisterStep" x-show="registerStep < 3" :disabled="!canAdvanceRegisterStep" class="btn-primary flex-1 rounded-lg py-3 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-60" style="display: none;">
                            {{ __('Next') }}
                        </button>
                        <button type="submit" x-show="registerStep === 3" :disabled="registerLoading || !canSubmitRegister" class="btn-primary flex-1 rounded-lg py-3 text-sm font-medium text-white transition-transform active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60" style="display: none;">
                            <span x-text="registerLoading ? @js(__('Please wait...')) : @js(__('Create workspace account'))"></span>
                        </button>
                    </div>
                </form>


            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('authSystem', (config = {}) => ({
                mode: config.initialMode === 'register' ? 'register' : 'login',
                register: {
                    fullname: '',
                    email: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    facility_name: '',
                    workgroup_name: '',
                    timezone: '',
                    tos: false,
                },
                registerLoading: false,
                registerError: '',
                registerSuccess: false,
                registerSuccessMessage: '',
                registerActivationEmail: '',
                registerStep: 1,
                registerEmailState: '',
                registerEmailStateMessage: '',
                registerUsernameState: '',
                registerUsernameMessage: '',
                timezoneOptions: @js($timezoneOptions),
                registerTimezoneOpen: false,
                registerTimezoneSearch: '',
                init() {
                    if (this.mode === 'register') {
                        this.resetRegisterFlow();
                        setTimeout(() => this.resetRegisterFlow(), 120);
                    }
                },
                get registerStepLabel() {
                    return ({
                        1: @js(__('Step 1 - Account details')),
                        2: @js(__('Step 2 - Secure your password')),
                        3: @js(__('Step 3 - Setup facility scope')),
                    })[this.registerStep] || '';
                },
                get registerPasswordMessage() {
                    const password = this.register.password;
                    if (!password) return '';

                    const issues = [];
                    if (password.length < 6) issues.push(@js(__('Password must be at least 6 characters long.')));
                    if (!/[A-Z]/.test(password)) issues.push(@js(__('Password must contain at least one uppercase letter.')));
                    if (!/[a-z]/.test(password)) issues.push(@js(__('Password must contain at least one lowercase letter.')));
                    if (!/[0-9]/.test(password)) issues.push(@js(__('Password must contain at least one digit.')));
                    if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) issues.push(@js(__('Password must contain at least one special character.')));
                    return issues.join('<br>');
                },
                get registerConfirmationMessage() {
                    if (!this.register.password_confirmation) return '';
                    return this.register.password === this.register.password_confirmation ? '' : @js(__('Passwords do not match.'));
                },
                get registerEmailMessage() {
                    const email = this.register.email.trim();
                    if (!email) return '';
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
                        ? ''
                        : @js(__('Please enter a valid email address, e.g. example@mail.com.'));
                },
                get canAdvanceRegisterStep() {
                    if (this.registerStep === 1) {
                        return !!this.register.fullname.trim()
                            && !!this.register.email.trim()
                            && !this.registerEmailMessage
                            && this.registerEmailState === 'available'
                            && !!this.register.username.trim()
                            && this.registerUsernameState === 'available';
                    }
                    if (this.registerStep === 2) {
                        return !!this.register.password && !!this.register.password_confirmation && !this.registerPasswordMessage && !this.registerConfirmationMessage;
                    }
                    return false;
                },
                nextRegisterStep() {
                    if (this.registerStep < 3 && this.canAdvanceRegisterStep) {
                        this.registerStep += 1;
                    }
                },
                prevRegisterStep() {
                    if (this.registerStep > 1) {
                        this.registerStep -= 1;
                    }
                },
                resetRegisterFlow() {
                    this.register = {
                        fullname: '',
                        email: '',
                        username: '',
                        password: '',
                        password_confirmation: '',
                        facility_name: '',
                        workgroup_name: '',
                        timezone: '',
                        tos: false,
                    };
                    this.registerError = '';
                    this.registerSuccess = false;
                    this.registerSuccessMessage = '';
                    this.registerActivationEmail = '';
                    this.registerLoading = false;
                    this.registerStep = 1;
                    this.registerEmailState = '';
                    this.registerEmailStateMessage = '';
                    this.registerUsernameState = '';
                    this.registerUsernameMessage = '';
                    this.registerTimezoneOpen = false;
                    this.registerTimezoneSearch = '';
                },
                backToLoginAfterRegister() {
                    window.location.href = @js(url('login'));
                },
                get canSubmitRegister() {
                    return !this.registerPasswordMessage && !this.registerConfirmationMessage && this.register.tos;
                },
                get selectedRegisterTimezoneLabel() {
                    const match = this.timezoneOptions.find(item => item.value === this.register.timezone);
                    return match ? match.label : @js(__('Timezone'));
                },
                get filteredRegisterTimezones() {
                    const keyword = this.registerTimezoneSearch.trim().toLowerCase();
                    if (!keyword) {
                        return this.timezoneOptions;
                    }

                    return this.timezoneOptions.filter(item =>
                        item.label.toLowerCase().includes(keyword) ||
                        item.value.toLowerCase().includes(keyword)
                    );
                },
                selectRegisterTimezone(value) {
                    this.register.timezone = value;
                    this.registerTimezoneOpen = false;
                    this.registerTimezoneSearch = '';
                },
                async checkRegisterEmail() {
                    const email = this.register.email.trim();
                    this.registerEmailStateMessage = '';

                    if (!email || this.registerEmailMessage) {
                        this.registerEmailState = '';
                        return;
                    }

                    this.registerEmailState = 'checking';

                    try {
                        const payload = new URLSearchParams({ email });
                        const response = await fetch('{{ url('check-email') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: payload,
                        });
                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.registerEmailState = 'taken';
                            this.registerEmailStateMessage = data.msg || @js(__('Unable to validate email.'));
                            return;
                        }

                        this.registerEmailState = data.available ? 'available' : 'taken';
                        this.registerEmailStateMessage = data.msg || '';
                    } catch (error) {
                        this.registerEmailState = 'taken';
                        this.registerEmailStateMessage = @js(__('Unable to validate email.'));
                    }
                },
                async checkRegisterUsername() {
                    const username = this.register.username.trim();
                    this.registerUsernameMessage = '';

                    if (!username) {
                        this.registerUsernameState = '';
                        return;
                    }

                    this.registerUsernameState = 'checking';

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
                            this.registerUsernameState = 'taken';
                            this.registerUsernameMessage = data.msg || @js(__('Unable to validate username.'));
                            return;
                        }

                        this.registerUsernameState = data.available ? 'available' : 'taken';
                        this.registerUsernameMessage = data.msg || '';
                    } catch (error) {
                        this.registerUsernameState = 'taken';
                        this.registerUsernameMessage = @js(__('Unable to validate username.'));
                    }
                },
                
                async checkLogin(event) {
                    const form = event.target;
                    const formData = new FormData(form);
                    const submitBtn = document.getElementById('submit_btn');
                    const errorBox = document.getElementById('login_error');
                    const successBox = document.getElementById('login_success');
                    
                    errorBox.classList.add('hidden');
                    successBox.classList.add('hidden');

                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerText = @js(__('Please wait...'));

                    try {
                        const data = await Perfectlum.postForm(form.action, formData);
                        if (!data || typeof data !== 'object') {
                            throw new Error(@js(__('Unable to sign in. Please refresh the page and try again.')));
                        }

                        if (!data.success) {
                            errorBox.textContent = data.msg || data.message || @js(__('Invalid Email or Password.'));
                            errorBox.classList.remove('hidden');
                            return;
                        }

                        successBox.textContent = data.msg || @js(__('Successfully Loggedin, redirecting...'));
                        successBox.classList.remove('hidden');
                        window.location.href = data.next || @js(url('dashboard'));
                    } catch (error) {
                        errorBox.textContent = @js(__('An error occurred: ')) + (error.message || @js(__('Unable to sign in.')));
                        errorBox.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.innerText = @js(__('Log in'));
                    }
                },

                async submitRegister() {
                    this.registerLoading = true;
                    this.registerError = '';
                    this.registerSuccess = false;
                    this.registerSuccessMessage = '';
                    this.registerActivationEmail = '';

                    try {
                        const payload = new URLSearchParams();
                        Object.entries(this.register).forEach(([key, value]) => {
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
                            this.registerError = data.msg || @js(__('Unable to create account.'));
                            return;
                        }

                        this.registerSuccess = true;
                        this.registerSuccessMessage = data.msg || @js(__('Account created successfully. Please check your email, including the spam folder, and activate your account before signing in.'));
                        this.registerActivationEmail = data.email || this.register.email;
                    } catch (error) {
                        this.registerError = @js(__('Unable to create account.'));
                    } finally {
                        this.registerLoading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
