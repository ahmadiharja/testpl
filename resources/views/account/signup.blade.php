<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Signup' }} | {{ $settings['Site name'] ?? 'PerfectLum' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
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
                        <p class="mb-3 text-sm font-semibold uppercase tracking-[0.28em] text-sky-300">Create Account</p>
                        <h1 class="max-w-xl text-4xl font-extrabold leading-tight">Register with the same Alpine and Tailwind workflow used by the admin pages.</h1>
                        <p class="mt-4 max-w-lg text-sm text-white/65">This replaces the old Bootstrap signup surface and keeps the onboarding stack aligned with the rest of the application.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:max-w-xl" x-data="signupForm()">
            <div class="rounded-[2rem] border border-white/10 bg-[#161820] p-8 shadow-2xl">
                <div class="mb-8">
                    <a href="{{ url('login') }}" class="inline-flex items-center gap-2 text-sm text-white/50 transition hover:text-white">
                        <span>&larr;</span> Back to login
                    </a>
                    <h2 class="mt-6 text-3xl font-extrabold tracking-tight">Create an account</h2>
                    <p class="mt-2 text-sm text-white/55">Provision your initial facility and workgroup in one flow.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-white/70">Full Name</label>
                            <input x-model="form.fullname" type="text" required class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">Email</label>
                            <input x-model="form.email" type="email" required class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">Username</label>
                            <input x-model="form.username" type="text" required autocomplete="off" class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">Password</label>
                            <input x-model="form.password" type="password" required autocomplete="off" class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">Confirm Password</label>
                            <input x-model="form.password_confirmation" type="password" required class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">Facility Name</label>
                            <input x-model="form.facility_name" type="text" required class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-white/70">Workgroup Name</label>
                            <input x-model="form.workgroup_name" type="text" required class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-white/70">Timezone</label>
                            <select x-model="form.timezone" required class="h-12 w-full rounded-xl border border-white/10 bg-[#0f1117] px-4 text-sm text-white outline-none transition focus:border-sky-400 focus:ring-2 focus:ring-sky-400/20">
                                <option value="">Select a timezone</option>
                                @foreach(timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}">{{ $timezone }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <p x-show="passwordMessage" x-html="passwordMessage" class="text-xs text-rose-300" style="display: none;"></p>
                    <p x-show="confirmationMessage" x-text="confirmationMessage" class="text-xs text-rose-300" style="display: none;"></p>

                    <label class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/70">
                        <input x-model="form.tos" type="checkbox" class="mt-1 h-4 w-4 rounded border-white/20 bg-[#0f1117] text-sky-500 focus:ring-sky-500/30">
                        <span>I agree to the <a href="https://qubyx.com/en/terms-conditions" target="_blank" class="text-sky-300 underline underline-offset-2">terms and conditions</a>.</span>
                    </label>

                    <div x-show="error" x-text="error" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300" style="display: none;"></div>
                    <div x-show="success" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300" style="display: none;">
                        Account created successfully. Redirecting to login...
                    </div>

                    <button type="submit" :disabled="loading || !canSubmit"
                            class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-text="loading ? 'Please wait...' : 'Create Account'"></span>
                    </button>
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
                get passwordMessage() {
                    const password = this.form.password;
                    if (!password) return '';

                    const issues = [];
                    if (password.length < 6) issues.push('Password must be at least 6 characters long.');
                    if (!/[A-Z]/.test(password)) issues.push('Password must contain at least one uppercase letter.');
                    if (!/[a-z]/.test(password)) issues.push('Password must contain at least one lowercase letter.');
                    if (!/[0-9]/.test(password)) issues.push('Password must contain at least one digit.');
                    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) issues.push('Password must contain at least one special character.');
                    return issues.join('<br>');
                },
                get confirmationMessage() {
                    if (!this.form.password_confirmation) return '';
                    return this.form.password === this.form.password_confirmation ? '' : 'Passwords do not match.';
                },
                get canSubmit() {
                    return !this.passwordMessage && !this.confirmationMessage && this.form.tos;
                },
                async submit() {
                    this.loading = true;
                    this.error = '';
                    this.success = false;

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
                            this.error = data.msg || 'Unable to create account.';
                            return;
                        }

                        this.success = true;
                        setTimeout(() => window.location.href = data.next || '{{ url('login') }}', 1200);
                    } catch (error) {
                        this.error = 'Unable to create account.';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</body>
</html>
