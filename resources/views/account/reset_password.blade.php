<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Reset Password' }} | {{ $settings['Site name'] ?? 'PerfectLum' }}</title>
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
                <div class="absolute inset-0 bg-gradient-to-br from-sky-500/10 via-transparent to-violet-500/10"></div>
                <div class="relative z-10 flex h-[520px] flex-col justify-between">
                    <img src="{{ url($settings['Site logo'] ?? 'assets/images/perfectlum-logo.png') }}" alt="Site Logo" class="h-10 w-auto">
                    <div>
                        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.28em] text-sky-300">Reset Password</p>
                        <h1 class="max-w-lg text-4xl font-extrabold leading-tight">Set a new password with the current Tailwind stack.</h1>
                        <p class="mt-4 max-w-md text-sm text-white/65">Use a strong password with uppercase, lowercase, number, and symbol.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:max-w-md" x-data="resetPasswordForm()">
            <div class="rounded-[2rem] border border-white/10 bg-[#161820] p-8 shadow-2xl">
                <div class="mb-8">
                    <a href="{{ url('login') }}" class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white">
                        <span>&larr;</span> Back to login
                    </a>
                    <h2 class="mt-6 text-3xl font-extrabold tracking-tight">Create a new password</h2>
                    <p class="mt-2 text-sm text-white/55">This password will replace the previous one.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    @csrf
                    <input type="hidden" x-model="token">

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-white/70">Email</label>
                        <input id="email" x-model="email" type="email" required
                               class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-white/70">New Password</label>
                        <input id="password" x-model="password" type="password" required
                               class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        <p x-show="passwordMessage" x-html="passwordMessage" class="mt-2 text-xs text-rose-300" style="display: none;"></p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-white/70">Confirm Password</label>
                        <input id="password_confirmation" x-model="passwordConfirmation" type="password" required
                               class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        <p x-show="confirmationMessage" x-text="confirmationMessage" class="mt-2 text-xs text-rose-300" style="display: none;"></p>
                    </div>

                    <div x-show="error" x-text="error" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300" style="display: none;"></div>
                    <div x-show="success" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300" style="display: none;">
                        Password updated successfully. Redirecting to login...
                    </div>

                    <button type="submit" :disabled="loading || !canSubmit"
                            class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-text="loading ? 'Please wait...' : 'Update Password'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function resetPasswordForm() {
            return {
                token: @js($token),
                email: @js($email ?? ''),
                password: '',
                passwordConfirmation: '',
                loading: false,
                error: '',
                success: false,
                get passwordMessage() {
                    const password = this.password;
                    if (!password) return '';

                    const issues = [];
                    if (password.length < 8) issues.push('Password must be at least 8 characters long.');
                    if (!/[A-Z]/.test(password)) issues.push('Password must contain at least one uppercase letter.');
                    if (!/[a-z]/.test(password)) issues.push('Password must contain at least one lowercase letter.');
                    if (!/[0-9]/.test(password)) issues.push('Password must contain at least one digit.');
                    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) issues.push('Password must contain at least one special character.');
                    return issues.join('<br>');
                },
                get confirmationMessage() {
                    if (!this.passwordConfirmation) return '';
                    return this.password === this.passwordConfirmation ? '' : 'Passwords do not match.';
                },
                get canSubmit() {
                    return !this.passwordMessage && !this.confirmationMessage && this.password && this.passwordConfirmation;
                },
                async submit() {
                    this.loading = true;
                    this.error = '';
                    this.success = false;

                    try {
                        const response = await fetch('{{ url('password/reset') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: new URLSearchParams({
                                token: this.token,
                                email: this.email,
                                password: this.password,
                                password_confirmation: this.passwordConfirmation,
                            }),
                        });

                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            this.error = data.message || data.msg || 'Unable to update password.';
                            return;
                        }

                        this.success = true;
                        setTimeout(() => window.location.href = '{{ url('login') }}', 1200);
                    } catch (error) {
                        this.error = 'Unable to update password.';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</body>
</html>
