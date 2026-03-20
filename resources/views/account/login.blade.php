<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Login') }} | {{ $settings['Site name'] ?? 'PerfectLum' }}</title>

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

    </style>
</head>
<body x-data="authSystem" class="min-h-screen flex items-center justify-center p-4 text-[#E2E1E6]">

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
                    <!-- Qubyx Logo -->
                    <div class="flex items-center">
                        <img src="{{ asset('assets/images/qubyx-logo.png') }}" alt="Qubyx" class="h-8 w-auto">
                    </div>

                    <a href="#" class="text-xs bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 transition-colors text-white py-2 px-4 rounded-full flex items-center gap-1 backdrop-filter">
                        {{ __('Back to website') }} <span class="text-[10px]">&rarr;</span>
                    </a>
                </div>

                <!-- Bottom Text Overlay -->
                <div class="absolute bottom-0 left-0 right-0 p-12 text-center z-10">
                    <h2 class="text-3xl font-medium text-white mb-6 leading-tight drop-shadow-md">Capturing Moments,<br>Creating Memories</h2>
                    <div class="flex justify-center space-x-2">
                        <div class="w-6 h-[2px] bg-white/40 rounded-full"></div>
                        <div class="w-6 h-[2px] bg-white/40 rounded-full"></div>
                        <div class="w-8 h-[2px] bg-white rounded-full"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Form Section -->
        <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col justify-center relative overflow-y-auto">
            
            <!-- Logo for mobile (hidden on desktop) -->
            <div class="md:hidden flex items-center mb-8">
                <img src="{{ asset('assets/images/qubyx-logo.png') }}" alt="Qubyx" class="h-8 w-auto">
            </div>

            <!-- ======================= LOGIN VIEW ======================= -->
            <div x-show="mode === 'login'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <h1 class="text-4xl font-semibold text-white mb-2">{{ __('Welcome back') }}</h1>
                <p class="text-muted text-sm mb-10">
                    {{ __("Don't have an account?") }}
                    <button @click="mode = 'register'" class="text-[#E2E1E6] hover:text-white underline decoration-[#8a8899] underline-offset-2 transition-colors">{{ __('Sign up') }}</button>
                </p>

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
                <h1 class="text-4xl font-semibold text-white mb-2 tracking-tight">{{ __('Create an account') }}</h1>
                <p class="text-muted text-sm mb-10">
                    {{ __('Already have an account?') }}
                    <button @click="mode = 'login'" class="text-[#E2E1E6] hover:text-white underline decoration-[#8a8899] underline-offset-2 transition-colors">{{ __('Log in') }}</button>
                </p>

                <form @submit.prevent="" class="space-y-4">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" placeholder="{{ __('First name') }}" class="w-full input-bg border text-sm rounded-lg px-4 py-3.5 text-white placeholder-[#8a8899] transition-all" required autofocus>
                        <input type="text" placeholder="{{ __('Last name') }}" class="w-full input-bg border text-sm rounded-lg px-4 py-3.5 text-white placeholder-[#8a8899] transition-all" required>
                    </div>

                    <div>
                        <input type="email" placeholder="{{ __('Email') }}" class="w-full input-bg border text-sm rounded-lg px-4 py-3.5 text-white placeholder-[#8a8899] transition-all" required>
                    </div>

                    <div class="relative">
                        <input type="password" placeholder="{{ __('Enter your password') }}" class="w-full input-bg border text-sm rounded-lg pl-4 pr-12 py-3.5 text-white placeholder-[#8a8899] transition-all" required>
                        <button type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-muted hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </button>
                    </div>

                    <div class="flex items-start mt-4 mb-2">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer group select-none mt-1">
                            <input type="checkbox" required class="custom-checkbox shrink-0 w-4 h-4 rounded appearance-none group-hover:ring-2 ring-white/20 transition-all">
                            <span class="text-sm font-medium leading-relaxed text-muted">{{ __('I agree to the') }} <a href="#" class="underline decoration-[#8a8899] hover:text-white transition-colors">{{ __('Terms & Conditions') }}</a></span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full text-white font-medium text-sm rounded-lg py-3.5 mt-2 transition-transform active:scale-[0.98]">
                        {{ __('Create account') }}
                    </button>
                    
                </form>


            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('authSystem', () => ({
                mode: 'login',
                
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
                        if (!data.success) {
                            errorBox.innerHTML = data.msg;
                            errorBox.classList.remove('hidden');
                            return;
                        }

                        successBox.innerHTML = data.msg;
                        successBox.classList.remove('hidden');
                        window.location.href = data.next;
                    } catch (error) {
                        errorBox.innerHTML = "An error occurred: " + error.message;
                        errorBox.classList.remove('hidden');
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        submitBtn.innerText = @js(__('Log in'));
                    }
                }
            }));
        });
    </script>
</body>
</html>
