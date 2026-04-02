@extends('mobile.layouts.auth')

@section('content')
    <div class="relative overflow-hidden p-4">
        <div class="absolute inset-x-0 top-0 h-28 bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.12),transparent_66%)]"></div>

        <div class="relative">
            <div class="flex items-center justify-between gap-3">
                <img src="{{ asset('assets/images/perfectlum-logo.png') }}" alt="PerfectLum" class="h-6 w-auto">
                <a href="{{ url('login?surface=desktop') }}" class="mobile-auth-pill">
                    Desktop
                </a>
            </div>

            <div class="mt-7">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-700/70">PerfectLum Mobile</p>
                <h1 class="mt-2 mobile-auth-heading">Sign in</h1>
                <p class="mt-1.5 max-w-sm mobile-auth-copy">
                    Due work, alerts, and sync in one handheld workspace.
                </p>
            </div>

            @if (session('idle_logout_notice'))
                <div class="mt-5 rounded-[1.05rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    {{ session('idle_logout_notice') }}
                </div>
            @endif

            <form method="POST" action="{{ url('login') }}" class="mt-6 space-y-3" x-data="mobileLogin()" @submit.prevent="submit">
                @csrf

                <div class="mobile-auth-surface px-4 py-3.5">
                    <label class="mobile-auth-field">
                        <span class="mobile-auth-label">Workspace ID</span>
                        <div class="mobile-auth-control flex items-center gap-3">
                            <i data-lucide="at-sign" class="h-4 w-4 text-slate-400"></i>
                            <input name="email" type="text" required autofocus placeholder="Email or username" class="w-full border-0 bg-transparent px-0 py-0 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                        </div>
                    </label>
                </div>

                <div class="mobile-auth-surface px-4 py-3.5">
                    <div class="flex items-center justify-between gap-3">
                        <label class="mobile-auth-label">Password</label>
                        <button type="button" @click="showPassword = !showPassword" class="text-[11px] font-semibold text-sky-700">
                            <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                        </button>
                    </div>
                    <div class="mobile-auth-control mt-2 flex items-center gap-3">
                        <i data-lucide="lock-keyhole" class="h-4 w-4 text-slate-400"></i>
                        <input name="password" :type="showPassword ? 'text' : 'password'" required placeholder="Enter your password" class="w-full border-0 bg-transparent px-0 py-0 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                    </div>
                </div>

                <label class="mobile-auth-surface flex items-center gap-3 px-4 py-3 text-sm text-slate-600">
                    <input name="remember" value="1" type="checkbox" class="h-4 w-4 rounded border-slate-300 bg-white text-sky-500 focus:ring-sky-500">
                    <span>Keep this device signed in</span>
                </label>

                <div id="mobile_login_error" class="mobile-auth-surface hidden border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>

                <button id="mobile_login_submit" type="submit" class="mobile-auth-button w-full rounded-[1.05rem] px-4 py-3.5 text-sm font-semibold text-white transition active:scale-[0.99]">
                    Open workspace
                </button>
            </form>

            <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
                <a href="{{ url('forgot-password') }}" class="transition hover:text-slate-900">Forgot password?</a>
                <a href="{{ url('login?surface=desktop') }}" class="transition hover:text-slate-900">Desktop sign in</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const register = () => window.Perfectlum.registerAlpineData('mobileLogin', () => ({
                    showPassword: false,
                    async submit(event) {
                        const form = event.target;
                        const formData = new FormData(form);
                        const submitBtn = document.getElementById('mobile_login_submit');
                        const errorBox = document.getElementById('mobile_login_error');

                        errorBox.classList.add('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-60', 'cursor-not-allowed');

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });

                            const data = await response.json();
                            if (!data.success) {
                                errorBox.textContent = data.msg || 'Unable to sign in.';
                                errorBox.classList.remove('hidden');
                                return;
                            }

                            const next = String(data.next || '');
                            if (next.includes('choose-platform')) {
                                window.location.href = @json(route('mobile.choose-platform'));
                                return;
                            }

                            window.location.href = @json(route('mobile.dashboard'));
                        } catch (error) {
                            errorBox.textContent = 'Unable to sign in right now. Please try again.';
                            errorBox.classList.remove('hidden');
                        } finally {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                        }
                    }
                }));

                if (window.Perfectlum?.registerAlpineData) {
                    register();
                    return;
                }

                (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(register);
            })();
        </script>
    @endpush
@endsection
