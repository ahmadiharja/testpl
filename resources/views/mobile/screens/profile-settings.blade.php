@extends('mobile.layouts.app')

@php
    $profileUser = $mobileUser;
    $profileName = $profileUser->fullname ?: $profileUser->name ?: 'User';
    $profileEmail = $profileUser->email ?: 'No email configured';
    $facilityLabel = $profileUser->facility_name ?: optional($profileUser->facility)->name ?: 'Assigned facility pending';
    $timezoneLabel = $profileUser->timezone ?: 'UTC';
    $profileUsername = $profileUser->name ?: '-';
    $profileImagePath = $profileUser->profile_image ?: null;
    $hasProfileImage = $profileImagePath && file_exists(public_path($profileImagePath));
    $profileImage = $hasProfileImage ? url($profileImagePath) : null;
    $profileParts = preg_split('/\s+/', trim($profileName)) ?: [];
    $profileInitials = strtoupper(substr($profileParts[0] ?? 'U', 0, 1) . substr($profileParts[1] ?? ($profileUser->name ?: ''), 0, 1));
@endphp

@push('head')
    <style>
        .mobile-profile-settings-shell {
            display: grid;
            gap: 1rem;
        }

        .mobile-profile-settings-context {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.95rem 1rem;
            border-radius: 1.35rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            box-shadow: 0 18px 38px -34px rgba(15, 23, 42, 0.26);
        }

        .mobile-profile-settings-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3.35rem;
            height: 3.35rem;
            overflow: hidden;
            border-radius: 1rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: linear-gradient(145deg, #0ea5e9, #2563eb);
            font-size: 0.96rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #fff;
            flex-shrink: 0;
        }

        .mobile-profile-settings-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mobile-profile-settings-card-kicker,
        .mobile-profile-settings-label {
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .mobile-profile-settings-context-kicker {
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-profile-settings-context-copy {
            margin-top: 0.16rem;
            font-size: 0.8rem;
            line-height: 1.55;
            color: #64748b;
        }

        .mobile-profile-settings-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.62rem;
        }

        .mobile-profile-settings-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            padding: 0.44rem 0.72rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.9);
            background: rgba(239, 246, 255, 0.92);
            font-size: 0.74rem;
            font-weight: 600;
            color: #0f172a;
        }

        .mobile-profile-settings-chip strong {
            color: #64748b;
            font-size: 0.6rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .mobile-profile-settings-card {
            overflow: hidden;
            border-radius: 1.55rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            box-shadow: 0 18px 40px -34px rgba(15, 23, 42, 0.38);
        }

        .mobile-profile-settings-card-head {
            padding: 1rem 1rem 0;
        }

        .mobile-profile-settings-card-kicker,
        .mobile-profile-settings-label {
            color: #94a3b8;
        }

        .mobile-profile-settings-card-title {
            margin-top: 0.35rem;
            font-size: 1.08rem;
            font-weight: 700;
            color: #0f172a;
        }

        .mobile-profile-settings-card-copy {
            margin-top: 0.3rem;
            font-size: 0.83rem;
            line-height: 1.55;
            color: #64748b;
        }

        .mobile-profile-settings-card-body {
            padding: 1rem;
        }

        .mobile-profile-settings-image-wrap {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.95rem;
            border-radius: 1.2rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: rgba(255, 255, 255, 0.92);
        }

        .mobile-profile-settings-image {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 4.3rem;
            height: 4.3rem;
            overflow: hidden;
            border-radius: 1.25rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: linear-gradient(145deg, #0ea5e9, #2563eb);
            color: #fff;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            flex-shrink: 0;
        }

        .mobile-profile-settings-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mobile-profile-settings-field-grid {
            display: grid;
            gap: 0.82rem;
            margin-top: 0.95rem;
        }

        .mobile-profile-settings-field {
            display: grid;
            gap: 0.42rem;
        }

        .mobile-profile-settings-input {
            width: 100%;
            min-height: 3rem;
            padding: 0.82rem 0.95rem;
            border-radius: 1rem;
            border: 1px solid rgba(203, 213, 225, 0.84);
            background: rgba(255, 255, 255, 0.96);
            font-size: 0.9rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease;
        }

        .mobile-profile-settings-input:focus {
            border-color: rgba(14, 165, 233, 0.72);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
        }

        .mobile-profile-settings-inline-actions,
        .mobile-profile-settings-form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .mobile-profile-settings-inline-actions {
            margin-top: 0.82rem;
        }

        .mobile-profile-settings-form-actions {
            margin-top: 1rem;
        }

        .mobile-profile-settings-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.42rem;
            min-height: 2.6rem;
            padding: 0.62rem 0.95rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.86);
            background: rgba(239, 246, 255, 0.94);
            font-size: 0.78rem;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-profile-settings-button.primary {
            border-color: transparent;
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
            color: #fff;
            box-shadow: 0 20px 34px -26px rgba(37, 99, 235, 0.72);
        }

        .mobile-profile-settings-button.rose {
            border-color: rgba(253, 164, 175, 0.8);
            background: rgba(255, 241, 242, 0.96);
            color: #be123c;
        }

        .mobile-profile-settings-flash {
            padding: 0.86rem 0.95rem;
            border-radius: 1.15rem;
            border: 1px solid rgba(125, 211, 252, 0.58);
            background: rgba(240, 249, 255, 0.94);
            color: #075985;
            font-size: 0.84rem;
            font-weight: 600;
        }

        .mobile-profile-settings-flash.error {
            border-color: rgba(253, 164, 175, 0.66);
            background: rgba(255, 241, 242, 0.94);
            color: #be123c;
        }
    </style>
@endpush

@section('content')
    <div class="mobile-profile-settings-shell">
        @if (session('success'))
            <div class="mobile-profile-settings-flash">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mobile-profile-settings-flash error">{{ session('error') }}</div>
        @endif

        <section class="mobile-profile-settings-context">
            <div class="mobile-profile-settings-avatar" id="mobile-profile-settings-hero-avatar">
                @if ($profileImage)
                    <img src="{{ $profileImage }}" alt="{{ $profileName }}" id="mobile-profile-settings-hero-image">
                @else
                    <span id="mobile-profile-settings-hero-fallback">{{ $profileInitials ?: 'U' }}</span>
                    <img src="" alt="{{ $profileName }}" id="mobile-profile-settings-hero-image" class="hidden">
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <p class="mt-0.5 break-words text-[1rem] font-semibold leading-tight text-slate-950">{{ $profileName }}</p>
                <p class="mobile-profile-settings-context-copy">Profile settings, password updates, and workstation credentials.</p>

                <div class="mobile-profile-settings-chip-row">
                    <span class="mobile-profile-settings-chip"><strong>Facility</strong> {{ $facilityLabel }}</span>
                    <span class="mobile-profile-settings-chip"><strong>Timezone</strong> {{ $timezoneLabel }}</span>
                </div>
            </div>
        </section>

        <section class="mobile-profile-settings-card">
            <div class="mobile-profile-settings-card-head">
                <p class="mobile-profile-settings-card-kicker">Account Profile</p>
                <h2 class="mobile-profile-settings-card-title">Identity, password, and image</h2>
                <p class="mobile-profile-settings-card-copy">Change the fields that define this account and keep sign-in credentials current.</p>
            </div>

            <div class="mobile-profile-settings-card-body">
                <form method="post" action="{{ route('mobile.profile.settings.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mobile-profile-settings-image-wrap">
                        <div class="mobile-profile-settings-image" id="mobile-profile-settings-preview-wrap">
                            @if ($profileImage)
                                <img src="{{ $profileImage }}" alt="{{ $profileName }}" id="mobile-profile-settings-preview">
                            @else
                                <span id="mobile-profile-settings-preview-fallback">{{ $profileInitials ?: 'U' }}</span>
                                <img src="" alt="{{ $profileName }}" id="mobile-profile-settings-preview" class="hidden">
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="mobile-profile-settings-label">Profile picture</p>
                            <p class="mt-2 text-[0.84rem] leading-6 text-slate-600">Use JPEG, PNG, or GIF. Recommended size 200×200 pixels.</p>

                            <div class="mobile-profile-settings-inline-actions">
                                <button type="button" class="mobile-profile-settings-button" onclick="document.getElementById('mobile-profile-settings-image-input').click()">
                                    <i data-lucide="upload" class="h-3.5 w-3.5"></i>
                                    Choose image
                                </button>
                                <button type="button" class="mobile-profile-settings-button rose" onclick="window.removeMobileProfileImage && window.removeMobileProfileImage()">
                                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="file" id="mobile-profile-settings-image-input" name="profile_image" accept="image/*" class="hidden">

                    <div class="mobile-profile-settings-field-grid">
                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">User name</span>
                            <input type="text" class="mobile-profile-settings-input" name="username" value="{{ $profileUser->name }}" required>
                        </label>

                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">Full name</span>
                            <input type="text" class="mobile-profile-settings-input" name="fname" value="{{ $profileUser->fullname }}">
                        </label>

                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">Email</span>
                            <input type="email" class="mobile-profile-settings-input" name="email" value="{{ $profileUser->email }}">
                        </label>

                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">Password</span>
                            <input type="password" class="mobile-profile-settings-input" name="password" placeholder="Leave blank to keep current password">
                        </label>

                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">Retype password</span>
                            <input type="password" class="mobile-profile-settings-input" name="password2" placeholder="Retype the new password">
                        </label>
                    </div>

                    <div class="mobile-profile-settings-form-actions">
                        <button type="submit" class="mobile-profile-settings-button primary">
                            <i data-lucide="save" class="h-3.5 w-3.5"></i>
                            Save profile changes
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="mobile-profile-settings-card">
            <div class="mobile-profile-settings-card-head">
                <p class="mobile-profile-settings-card-kicker">Remote Credentials</p>
                <h2 class="mobile-profile-settings-card-title">Workstation connection account</h2>
                <p class="mobile-profile-settings-card-copy">Set the credentials that the workstation client uses when it connects back to the platform.</p>
            </div>

            <div class="mobile-profile-settings-card-body">
                <form method="post" action="{{ route('mobile.profile.settings.update') }}">
                    @csrf

                    <div class="mobile-profile-settings-field-grid">
                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">Remote user</span>
                            <input type="text" class="mobile-profile-settings-input" name="remote_user" value="{{ $profileUser->sync_user }}">
                        </label>

                        <label class="mobile-profile-settings-field">
                            <span class="mobile-profile-settings-label">Remote password</span>
                            <input type="text" class="mobile-profile-settings-input" id="mobile-profile-settings-remote-password" name="remote_password" value="{{ $profileUser->sync_password_raw }}">
                        </label>
                    </div>

                    <div class="mobile-profile-settings-inline-actions">
                        <button type="button" class="mobile-profile-settings-button" onclick="window.copyMobileProfileSettingsValue && window.copyMobileProfileSettingsValue(document.getElementById('mobile-profile-settings-remote-password').value)">
                            <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                            Copy password
                        </button>
                        <button type="button" class="mobile-profile-settings-button" onclick="window.generateMobileProfilePassword && window.generateMobileProfilePassword()">
                            <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
                            Generate password
                        </button>
                    </div>

                    <div class="mobile-profile-settings-form-actions">
                        <button type="submit" class="mobile-profile-settings-button primary">
                            <i data-lucide="save" class="h-3.5 w-3.5"></i>
                            Update credentials
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        window.copyMobileProfileSettingsValue = function (value) {
            if (!value) {
                return;
            }

            if (navigator.clipboard?.writeText) {
                navigator.clipboard.writeText(value).catch(function () {});
                return;
            }

            const field = document.createElement('textarea');
            field.value = value;
            field.setAttribute('readonly', '');
            field.style.position = 'absolute';
            field.style.left = '-9999px';
            document.body.appendChild(field);
            field.select();

            try {
                document.execCommand('copy');
            } catch (error) {}

            document.body.removeChild(field);
        };

        window.generateMobileProfilePassword = function (length = 9) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';

            for (let i = 0; i < length; i += 1) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }

            const field = document.getElementById('mobile-profile-settings-remote-password');
            if (field) {
                field.value = result;
            }
        };

        window.removeMobileProfileImage = async function () {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));

            try {
                const response = await window.Perfectlum.postForm(@json(route('mobile.profile.remove-image')), formData);
                if (response?.success) {
                    window.location.reload();
                }
            } catch (error) {}
        };

        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('mobile-profile-settings-image-input');
            if (!input) {
                return;
            }

            input.addEventListener('change', function (event) {
                const file = event.target.files?.[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function () {
                    const preview = document.getElementById('mobile-profile-settings-preview');
                    const heroImage = document.getElementById('mobile-profile-settings-hero-image');
                    const previewFallback = document.getElementById('mobile-profile-settings-preview-fallback');
                    const heroFallback = document.getElementById('mobile-profile-settings-hero-fallback');

                    if (previewFallback) {
                        previewFallback.classList.add('hidden');
                    }

                    if (heroFallback) {
                        heroFallback.classList.add('hidden');
                    }

                    if (preview) {
                        preview.classList.remove('hidden');
                        preview.src = reader.result;
                    }

                    if (heroImage) {
                        heroImage.classList.remove('hidden');
                        heroImage.src = reader.result;
                    }
                };

                reader.readAsDataURL(file);
            });
        });
    </script>
@endpush
