<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('Forgot Password') }} | {{ $settings['Site name'] ?? 'PerfectLum' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#0f1117] text-white">
    <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-4 py-8 lg:flex-row lg:items-center lg:gap-10">
        <div class="hidden flex-1 lg:block">
            <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-[#161820] p-10 shadow-2xl">
                <img src="{{ asset('assets/images/dune_background.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-25">
                <div class="absolute inset-0 bg-gradient-to-br from-sky-500/10 via-transparent to-emerald-500/10"></div>
                <div class="relative z-10 flex h-[520px] flex-col justify-between">
                    <img src="{{ url($settings['Site logo'] ?? 'assets/images/perfectlum-logo.png') }}" alt="Site Logo" class="h-10 w-auto">
                    <div>
                        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.28em] text-sky-300">{{ __('Account Recovery') }}</p>
                        <h1 class="max-w-lg text-4xl font-extrabold leading-tight">{{ __('Reset account access without returning to the legacy UI.') }}</h1>
                        <p class="mt-4 max-w-md text-sm text-white/65">{{ __('Enter your email or username and the system will send a password reset link to the registered email address.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:max-w-md" x-data="forgotPasswordForm()">
            <div class="rounded-[2rem] border border-white/10 bg-[#161820] p-8 shadow-2xl">
                <div class="mb-8">
                    <a href="{{ url('login') }}" class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white">
                        <span>&larr;</span> {{ __('Back to login') }}
                    </a>
                    <h2 class="mt-6 text-3xl font-extrabold tracking-tight">{{ __('Forgot password') }}</h2>
                    <p class="mt-2 text-sm text-white/55">{{ __('We will email a reset link if the account exists.') }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-white/70">{{ __('Email / Username') }}</label>
                        <input id="email" x-model="email" type="text" required
                               class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                    </div>

                    <div x-show="error" x-text="error" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300" style="display: none;"></div>

                    <div x-show="success" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300" style="display: none;">
                        {{ __('We have emailed you a password reset link.') }}
                    </div>

                    <button type="submit" :disabled="loading"
                            class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-text="loading ? @js(__('Please wait...')) : @js(__('Send Reset Link'))"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function forgotPasswordForm() {
            return {
                email: '',
                loading: false,
                error: '',
                success: false,
                async submit() {
                    this.loading = true;
                    this.error = '';
                    this.success = false;

                    try {
                        const response = await fetch('{{ url('reset-password/email') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: new URLSearchParams({ email: this.email }),
                        });
                        const data = await response.json();

                        if (data.success === 'passwords.user') {
                            this.error = @js(__("We can't find a user with this e-mail address."));
                        } else if (!data.success) {
                            this.error = data.msg || @js(__('Unable to send reset link.'));
                        } else {
                            this.success = true;
                        }
                    } catch (error) {
                        this.error = @js(__('Unable to send reset link.'));
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</body>
</html>
