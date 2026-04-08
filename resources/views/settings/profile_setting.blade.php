@include('common.navigations.header')

@php
    $profileUser = $user ?? auth()->user();
    $roleLabel = session()->get('role') ?: ($profileUser->role ?? '-');
    $facilityLabel = $profileUser->facility_name ?: optional($profileUser->facility)->name ?: '-';
    $workgroupLabel = $profileUser->workgroup_name ?: '-';
    $timezoneLabel = $profileUser->timezone ?: 'UTC';
    $profileName = $profileUser->fullname ?: $profileUser->name ?: 'User';
    $profileParts = preg_split('/\s+/', trim($profileName)) ?: [];
    $profileInitials = strtoupper(substr($profileParts[0] ?? 'U', 0, 1) . substr($profileParts[1] ?? ($profileUser->name ?: ''), 0, 1));
    $profileImagePath = $profileUser->profile_image ?: null;
    $hasProfileImage = $profileImagePath && file_exists(public_path($profileImagePath));
    $profileImage = $hasProfileImage ? url($profileImagePath) : null;
@endphp

<div class="flex flex-col gap-6 pb-8">
    <x-page-header
        title="Profile Settings"
        description="Manage your profile, identity, and the remote credentials used to connect managed workstations."
        icon="user-round" />

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="mb-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Account Profile</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Personal details and account security</h2>
            <p class="mt-2 text-sm text-slate-500">Update your account information and keep your login credentials current.</p>
        </div>

        <form method="post" enctype="multipart/form-data" class="space-y-6">
            {{ csrf_field() }}

            <div class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm">
                            @if ($profileImage)
                                <img
                                    src="{{ $profileImage }}"
                                    alt="Profile Image"
                                    id="profile_image"
                                    class="h-full w-full object-cover">
                            @else
                                <div id="profile_image_fallback" class="flex h-full w-full items-center justify-center bg-gradient-to-br from-sky-500 to-indigo-600 text-3xl font-bold text-white">
                                    {{ $profileInitials ?: 'U' }}
                                </div>
                                <img
                                    src=""
                                    alt="Profile Image"
                                    id="profile_image"
                                    class="hidden h-full w-full object-cover">
                            @endif
                        </div>
                        <p class="mt-4 text-base font-semibold text-slate-900">{{ $profileUser->fullname ?: $profileUser->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $profileUser->email ?: 'No email configured' }}</p>
                        <p class="mt-4 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Profile Picture</p>
                        <p class="mt-2 text-xs leading-5 text-slate-500">Use JPEG, PNG, or GIF. Recommended size 200x200 pixels. Maximum 5 MB.</p>

                        <input type="file" id="imageUpload" name="profile_image" accept="image/*" class="hidden">

                        <div class="mt-5 flex flex-wrap justify-center gap-3">
                            <button
                                type="button"
                                onclick="document.getElementById('imageUpload').click();"
                                class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-sky-600 transition hover:border-sky-200 hover:bg-sky-50">
                                <i data-lucide="upload" class="h-4 w-4"></i>
                                Choose Image
                            </button>
                            <button
                                type="button"
                                onclick="remove_image()"
                                class="inline-flex h-10 items-center gap-2 rounded-xl border border-rose-200 bg-white px-4 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Facility</p>
                            <p class="mt-3 text-sm font-semibold text-slate-900">{{ $facilityLabel }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Workgroup</p>
                            <p class="mt-3 text-sm font-semibold text-slate-900">{{ $workgroupLabel }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">User Level</p>
                            <p class="mt-3 text-sm font-semibold capitalize text-slate-900">{{ $roleLabel ?: '-' }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Timezone</p>
                            <p class="mt-3 text-sm font-semibold text-slate-900">{{ $timezoneLabel }}</p>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">User Name</span>
                                <input
                                    class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                    type="text"
                                    name="username"
                                    value="{{ $profileUser->name }}"
                                    required>
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Full Name</span>
                                <input
                                    class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                    type="text"
                                    name="fname"
                                    value="{{ $profileUser->fullname }}">
                            </label>
                            <label class="space-y-2 md:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Email</span>
                                <input
                                    class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                    type="email"
                                    name="email"
                                    value="{{ $profileUser->email }}">
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Password</span>
                                <input
                                    class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                    type="password"
                                    name="password"
                                    placeholder="Leave blank to keep current password">
                            </label>
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Retype Password</span>
                                <input
                                    class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                    type="password"
                                    name="password2"
                                    placeholder="Retype new password">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-400"
                    type="submit">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Save Profile Changes
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="mb-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Remote Credentials</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Credentials used to connect workstations</h2>
            <p class="mt-2 text-sm text-slate-500">Manage the remote username and password used by the client application to communicate with this remote platform.</p>
        </div>

        <form method="post" class="space-y-6">
            {{ csrf_field() }}

            <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                <label class="space-y-2">
                    <span class="text-sm font-semibold text-slate-700">Remote User</span>
                    <input
                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                        type="text"
                        name="remote_user"
                        value="{{ $profileUser->sync_user }}">
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-semibold text-slate-700">Remote Password</span>
                    <div class="relative">
                        <input
                            class="h-12 w-full rounded-2xl border border-slate-200 px-4 pr-24 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                            type="text"
                            name="remote_password"
                            value="{{ $profileUser->sync_password_raw }}"
                            id="remote_password">
                        <button
                            type="button"
                            onclick="copy_field('#remote_password')"
                            class="absolute right-3 top-1/2 inline-flex h-9 -translate-y-1/2 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                            <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                            Copy
                        </button>
                    </div>
                </label>
            </div>

            <div class="flex flex-wrap justify-end gap-3">
                <button
                    class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    type="button"
                    onclick="generate_password()">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    Generate Password
                </button>
                <button
                    class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-400"
                    type="submit">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Update Credentials
                </button>
            </div>
        </form>
    </section>
</div>

<script>
    function generate_password(length = 9) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('remote_password').value = result;
    }

    document.getElementById('imageUpload')?.addEventListener('change', function (event) {
        const file = event.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function () {
            const image = document.getElementById('profile_image');
            const fallback = document.getElementById('profile_image_fallback');
            if (fallback) fallback.classList.add('hidden');
            if (image) {
                image.classList.remove('hidden');
                image.src = reader.result;
            }
        };
        reader.readAsDataURL(file);
    });

    async function remove_image() {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const data = await Perfectlum.postForm("{{ url('remove-image') }}", formData);
            if (data.success) {
                window.location = '';
            }
        } catch (error) {
        }
    }
</script>

@include('common.navigations.footer')
