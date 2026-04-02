@extends('mobile.layouts.auth')

@section('content')
    <div class="relative overflow-hidden p-4">
        <div class="absolute inset-x-0 top-0 h-28 bg-[radial-gradient(circle_at_top,rgba(14,165,233,0.1),transparent_66%)]"></div>

        <div class="relative">
            <div class="flex items-center justify-between gap-3">
                <img src="{{ asset('assets/images/perfectlum-logo.png') }}" alt="PerfectLum" class="h-6 w-auto">
                <span class="mobile-auth-pill">
                    Platform
                </span>
            </div>

            <div class="mt-7">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-700/70">Workspace Choice</p>
                <h1 class="mt-2 mobile-auth-heading">Choose workspace</h1>
                <p class="mt-1.5 mobile-auth-copy">
                    {{ $mobileUser->fullname ?: $mobileUser->name }} can open either platform on this device.
                </p>
            </div>

            <div class="mt-5 space-y-3">
                <a href="{{ route('mobile.select-platform', 'perfectlum') }}" class="mobile-auth-surface block border-sky-200 bg-sky-50/88 px-4 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.22em] text-sky-700">Primary</span>
                                <span class="text-[11px] font-medium text-sky-700/70">Remote calibration</span>
                            </div>
                            <p class="mt-3 text-[1.05rem] font-semibold text-slate-950">PerfectLum</p>
                            <p class="mt-1 text-[13px] leading-5 text-slate-600">
                                Due tasks, display alerts, and sync monitoring for the main workspace.
                            </p>
                        </div>
                        <i data-lucide="arrow-up-right" class="mt-1 h-4 w-4 shrink-0 text-sky-700"></i>
                    </div>
                </a>

                <a href="{{ route('mobile.select-platform', 'perfectchroma') }}" class="mobile-auth-surface block bg-white/92 px-4 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-600">Alternate</span>
                                <span class="text-[11px] font-medium text-slate-500">Companion workspace</span>
                            </div>
                            <p class="mt-3 text-[1.05rem] font-semibold text-slate-950">PerfectChroma</p>
                            <p class="mt-1 text-[13px] leading-5 text-slate-600">
                                Open the companion platform with the same mobile session.
                            </p>
                        </div>
                        <i data-lucide="arrow-up-right" class="mt-1 h-4 w-4 shrink-0 text-slate-500"></i>
                    </div>
                </a>
            </div>

            <div class="mobile-auth-surface mt-4 flex items-center justify-between px-4 py-3 text-sm text-slate-600">
                <div>
                    <p class="font-medium text-slate-900">{{ $mobileUser->email }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">Signed in and ready to continue.</p>
                </div>
                <a href="{{ url('logout') }}" class="font-semibold text-rose-600">Sign out</a>
            </div>
        </div>
    </div>
@endsection
