@extends('mobile.layouts.app')

@php
    $settings = $siteSettingsData ?? [];
    $smtp = $smtpDetails;
    $siteLogo = !empty($settings['Site logo']) ? url($settings['Site logo']) : '';
    $favicon = !empty($settings['favicon']) ? url($settings['favicon']) : '';
    $initialTab = $siteSettingsInitialTab ?? 'branding';
@endphp

@push('head')
    <style>
        .mobile-settings-page {
            display: grid;
            gap: 1rem;
        }

        .mobile-settings-page-intro,
        .mobile-settings-page-panel {
            border-radius: 1.35rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.04);
        }

        .mobile-settings-page-intro {
            padding: 1rem;
        }

        .mobile-settings-page-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-settings-page-title {
            margin-top: 0.28rem;
            font-size: 1.45rem;
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.04em;
            color: #0f172a;
        }

        .mobile-settings-page-copy {
            margin-top: 0.55rem;
            font-size: 12.5px;
            line-height: 1.65;
            color: #64748b;
        }

        .mobile-settings-tabs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.4rem;
            padding: 0.4rem;
            border-radius: 999px;
            background: rgba(248, 250, 252, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.95);
        }

        .mobile-settings-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.45rem;
            border-radius: 999px;
            border: 1px solid transparent;
            padding: 0.45rem 0.7rem;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            transition: 160ms ease;
        }

        .mobile-settings-tab.active {
            border-color: rgba(191, 219, 254, 0.82);
            background: rgba(239, 246, 255, 0.95);
            color: #0369a1;
        }

        .mobile-settings-page-panel {
            padding: 1rem;
        }

        .mobile-settings-section-title {
            font-size: 1.02rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .mobile-settings-section-copy {
            margin-top: 0.25rem;
            font-size: 12px;
            line-height: 1.55;
            color: #64748b;
        }

        .mobile-settings-form {
            display: grid;
            gap: 0.9rem;
            margin-top: 0.95rem;
        }

        .mobile-settings-form-grid {
            display: grid;
            gap: 0.8rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mobile-settings-field {
            display: grid;
            gap: 0.35rem;
        }

        .mobile-settings-field.full {
            grid-column: 1 / -1;
        }

        .mobile-settings-field label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-settings-field input,
        .mobile-settings-field textarea {
            width: 100%;
            min-width: 0;
            border-radius: 1rem;
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: rgba(255, 255, 255, 0.98);
            padding: 0.82rem 0.9rem;
            font-size: 13px;
            color: #0f172a;
            outline: none;
            transition: 150ms ease;
        }

        .mobile-settings-field input:focus,
        .mobile-settings-field textarea:focus {
            border-color: rgba(14, 165, 233, 0.55);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        }

        .mobile-settings-field textarea {
            min-height: 7.5rem;
            resize: vertical;
        }

        .mobile-settings-preview-grid {
            display: grid;
            gap: 0.8rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mobile-settings-preview-card {
            border-radius: 1.1rem;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(248, 250, 252, 0.95);
            padding: 0.9rem;
        }

        .mobile-settings-preview-card img {
            display: block;
            width: 100%;
            height: 4.6rem;
            object-fit: contain;
            border-radius: 0.85rem;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            padding: 0.65rem;
        }

        .mobile-settings-upload {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.35rem;
            border-radius: 999px;
            border: 1px solid rgba(14, 165, 233, 0.18);
            background: rgba(239, 246, 255, 0.92);
            padding: 0.45rem 0.85rem;
            font-size: 12px;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-settings-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 2.85rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            padding: 0.78rem 1rem;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 16px 30px rgba(14, 165, 233, 0.22);
        }

        .mobile-settings-flash {
            border-radius: 1rem;
            padding: 0.8rem 0.9rem;
            font-size: 12px;
            line-height: 1.55;
        }

        .mobile-settings-flash.success {
            border: 1px solid rgba(134, 239, 172, 0.7);
            background: rgba(240, 253, 244, 0.92);
            color: #166534;
        }

        .mobile-settings-flash.error {
            border: 1px solid rgba(253, 164, 175, 0.7);
            background: rgba(255, 241, 242, 0.92);
            color: #be123c;
        }

        .mobile-release-radio-grid {
            display: grid;
            gap: 0.7rem;
        }

        .mobile-release-radio {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(248, 250, 252, 0.92);
            padding: 0.85rem 0.9rem;
        }

        .mobile-release-radio input {
            margin-top: 0.18rem;
        }

        .mobile-release-version-grid {
            display: grid;
            gap: 0.8rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mobile-release-version {
            border-radius: 1rem;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(248, 250, 252, 0.95);
            padding: 0.9rem;
        }

        .mobile-release-version.next {
            border-color: rgba(134, 239, 172, 0.7);
            background: rgba(240, 253, 244, 0.92);
        }

        .mobile-release-version-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-release-version-value {
            margin-top: 0.45rem;
            font-size: 1.2rem;
            font-weight: 800;
            line-height: 1.1;
            color: #0f172a;
            letter-spacing: -0.04em;
        }

        .mobile-release-version.next .mobile-release-version-value {
            color: #15803d;
        }
    </style>
@endpush

@section('content')
    <div class="mobile-settings-page" x-data="{ activeTab: @js($initialTab), releaseType: 'build' }">
        <section class="mobile-settings-page-intro">
            <p class="mobile-settings-page-kicker">Site settings</p>
            <h2 class="mobile-settings-page-title">Branding, delivery, and release</h2>
            <p class="mobile-settings-page-copy">This mobile workspace mirrors the desktop site settings area. Update branding, configure SMTP delivery, and prepare the next application release from the same screen.</p>
        </section>

        @if(session('success'))
            <div class="mobile-settings-flash success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mobile-settings-flash error">{{ session('error') }}</div>
        @endif

        <section class="mobile-settings-page-panel">
            <div class="mobile-settings-tabs">
                <button type="button" class="mobile-settings-tab" :class="{ 'active': activeTab === 'branding' }" @click="activeTab = 'branding'">Branding</button>
                <button type="button" class="mobile-settings-tab" :class="{ 'active': activeTab === 'smtp' }" @click="activeTab = 'smtp'">SMTP</button>
                <button type="button" class="mobile-settings-tab" :class="{ 'active': activeTab === 'release' }" @click="activeTab = 'release'">Release</button>
            </div>
        </section>

        <section class="mobile-settings-page-panel" x-show="activeTab === 'branding'" x-cloak>
            <h3 class="mobile-settings-section-title">Brand identity</h3>
            <p class="mobile-settings-section-copy">Keep the site name, logo, favicon, and sender defaults aligned with the remote workspace seen by your users.</p>

            <form method="post" enctype="multipart/form-data" class="mobile-settings-form">
                @csrf
                <input type="hidden" name="settings_action" value="branding">

                <div class="mobile-settings-preview-grid">
                    <article class="mobile-settings-preview-card">
                        <div class="mobile-settings-field">
                            <label>Site logo</label>
                        </div>
                        <img src="{{ $siteLogo }}" alt="Site logo preview" id="mobile-site-logo-preview">
                        <div class="mt-3">
                            <input type="file" id="mobile-site-logo-input" name="site_logo" accept="image/*" class="hidden">
                            <button type="button" class="mobile-settings-upload" onclick="document.getElementById('mobile-site-logo-input').click()">Choose logo</button>
                        </div>
                    </article>

                    <article class="mobile-settings-preview-card">
                        <div class="mobile-settings-field">
                            <label>Favicon</label>
                        </div>
                        <img src="{{ $favicon }}" alt="Favicon preview" id="mobile-site-favicon-preview">
                        <div class="mt-3">
                            <input type="file" id="mobile-site-favicon-input" name="favicon" accept="image/*" class="hidden">
                            <button type="button" class="mobile-settings-upload" onclick="document.getElementById('mobile-site-favicon-input').click()">Choose favicon</button>
                        </div>
                    </article>
                </div>

                <div class="mobile-settings-form-grid">
                    <div class="mobile-settings-field full">
                        <label>Site name</label>
                        <input type="text" name="site" value="{{ $settings['Site name'] ?? '' }}" required>
                    </div>
                    <div class="mobile-settings-field">
                        <label>Default sender email</label>
                        <input type="email" name="email" value="{{ $smtp?->senderemail }}">
                    </div>
                    <div class="mobile-settings-field">
                        <label>Default sender name</label>
                        <input type="text" name="sender" value="{{ $smtp?->sendername }}">
                    </div>
                </div>

                <button type="submit" class="mobile-settings-submit">Save site settings</button>
            </form>
        </section>

        <section class="mobile-settings-page-panel" x-show="activeTab === 'smtp'" x-cloak>
            <h3 class="mobile-settings-section-title">SMTP delivery</h3>
            <p class="mobile-settings-section-copy">Configure the outbound mail transport used by alerts, test email, and other notification flows.</p>

            <form method="post" class="mobile-settings-form">
                @csrf
                <input type="hidden" name="settings_action" value="smtp">

                <div class="mobile-settings-form-grid">
                    <div class="mobile-settings-field">
                        <label>Host</label>
                        <input type="text" name="host" value="{{ $smtp?->host }}">
                    </div>
                    <div class="mobile-settings-field">
                        <label>Port</label>
                        <input type="text" name="port" value="{{ $smtp?->port }}">
                    </div>
                    <div class="mobile-settings-field">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ $smtp?->username }}">
                    </div>
                    <div class="mobile-settings-field">
                        <label>Password</label>
                        <input type="password" name="password" value="{{ $smtp?->password }}">
                    </div>
                    <div class="mobile-settings-field">
                        <label>Sender email</label>
                        <input type="text" name="sender_email" value="{{ $smtp?->senderemail }}">
                    </div>
                    <div class="mobile-settings-field">
                        <label>Sender name</label>
                        <input type="text" name="sender_name" value="{{ $smtp?->sendername }}">
                    </div>
                </div>

                <button type="submit" class="mobile-settings-submit">Save SMTP settings</button>
            </form>
        </section>

        <section class="mobile-settings-page-panel" x-show="activeTab === 'release'" x-cloak>
            <h3 class="mobile-settings-section-title">Release builder</h3>
            <p class="mobile-settings-section-copy">Choose how the version should change, review the semantic version preview, and add a short release note before dispatching a new build.</p>

            <form method="post" class="mobile-settings-form">
                @csrf
                <input type="hidden" name="settings_action" value="release">

                <div class="mobile-release-version-grid">
                    <article class="mobile-release-version">
                        <p class="mobile-release-version-label">Current version</p>
                        <p id="mobile-current-version" class="mobile-release-version-value">{{ \App\Helpers\common_functions::appVersion() }}</p>
                    </article>
                    <article class="mobile-release-version next">
                        <p class="mobile-release-version-label">Next version</p>
                        <p id="mobile-next-version" class="mobile-release-version-value"></p>
                        <input type="hidden" id="mobile-hidden-next-version" name="next_version">
                    </article>
                </div>

                <div class="mobile-release-radio-grid">
                    <label class="mobile-release-radio">
                        <input type="radio" name="type" value="build" x-model="releaseType">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Build</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Increment the patch or build segment only.</span>
                        </span>
                    </label>
                    <label class="mobile-release-radio">
                        <input type="radio" name="type" value="minor" x-model="releaseType">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Minor</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Increment the middle version and reset build.</span>
                        </span>
                    </label>
                    <label class="mobile-release-radio">
                        <input type="radio" name="type" value="major" x-model="releaseType">
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">Major</span>
                            <span class="mt-1 block text-xs leading-5 text-slate-500">Increment the major version and reset lower segments.</span>
                        </span>
                    </label>
                </div>

                <div class="mobile-settings-field">
                    <label>Build comment</label>
                    <textarea name="comment" placeholder="Summarize what changed in this release..."></textarea>
                </div>

                <button type="submit" class="mobile-settings-submit">Create build</button>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const bindPreview = (inputId, imageId) => {
                const input = document.getElementById(inputId);
                const image = document.getElementById(imageId);
                if (!input || !image) return;
                input.addEventListener('change', (event) => {
                    const file = event.target.files?.[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = () => {
                        image.src = reader.result;
                    };
                    reader.readAsDataURL(file);
                });
            };

            bindPreview('mobile-site-logo-input', 'mobile-site-logo-preview');
            bindPreview('mobile-site-favicon-input', 'mobile-site-favicon-preview');

            const currentVersionEl = document.getElementById('mobile-current-version');
            const nextVersionEl = document.getElementById('mobile-next-version');
            const hiddenNextVersionEl = document.getElementById('mobile-hidden-next-version');
            const releaseInputs = document.querySelectorAll('input[name="type"]');

            const computeNextVersion = (type) => {
                const currentVersion = (currentVersionEl?.textContent || '0.0.0').trim();
                const parts = currentVersion.split('.').map((item) => parseInt(item, 10) || 0);
                let [major, minor, build] = [parts[0] || 0, parts[1] || 0, parts[2] || 0];

                if (type === 'build') {
                    build += 1;
                } else if (type === 'minor') {
                    minor += 1;
                    build = 0;
                } else if (type === 'major') {
                    major += 1;
                    minor = 0;
                    build = 0;
                }

                const nextVersion = `${major}.${minor}.${build}`;
                if (nextVersionEl) nextVersionEl.textContent = nextVersion;
                if (hiddenNextVersionEl) hiddenNextVersionEl.value = nextVersion;
            };

            releaseInputs.forEach((input) => {
                input.addEventListener('change', () => computeNextVersion(input.value));
            });

            computeNextVersion(document.querySelector('input[name="type"]:checked')?.value || 'build');
        })();
    </script>
@endpush
