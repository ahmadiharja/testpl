<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mobile Workspace' }} | {{ $siteName ?? ($settings['Site name'] ?? 'PerfectLum') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --mobile-auth-radius: 1.05rem;
            --mobile-auth-control-height: 2.8rem;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top, rgba(14, 165, 233, 0.08), transparent 28%),
                linear-gradient(180deg, #f8fbff 0%, #f5f8fc 42%, #f6f8fc 100%);
        }

        .mobile-auth-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.92);
            box-shadow: 0 24px 56px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(14px);
        }

        .mobile-auth-input {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(148, 163, 184, 0.24);
            color: #0f172a;
        }

        .mobile-auth-input:focus {
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
            outline: none;
        }

        .mobile-auth-button {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        }

        .mobile-auth-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(255, 255, 255, 0.78);
            padding: 0.34rem 0.74rem;
            font-size: 10.5px;
            font-weight: 600;
            color: #64748b;
        }

        .mobile-auth-heading {
            font-size: 1.52rem;
            font-weight: 650;
            letter-spacing: -0.03em;
            line-height: 1.08;
            color: #0f172a;
        }

        .mobile-auth-copy {
            font-size: 12.5px;
            line-height: 1.5;
            color: #64748b;
        }

        .mobile-auth-surface {
            border-radius: calc(var(--mobile-auth-radius) + 0.08rem);
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.03);
        }

        .mobile-auth-field {
            display: grid;
            gap: 0.38rem;
        }

        .mobile-auth-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #64748b;
        }

        .mobile-auth-control {
            min-height: var(--mobile-auth-control-height);
        }
    </style>
</head>
@php
    $idleLogoutEnabled = !empty($idleLogoutEnabled);
@endphp
<body class="min-h-screen text-slate-900"
      @if($idleLogoutEnabled)
          data-surface="mobile"
          data-idle-logout-minutes="{{ config('session.idle_timeout', 30) }}"
          data-idle-heartbeat-seconds="{{ config('session.idle_heartbeat_seconds', 60) }}"
          data-idle-heartbeat-url="{{ url('session/heartbeat') }}"
          data-idle-logout-url="{{ url('logout?reason=inactive') }}"
          data-idle-login-url="{{ route('mobile.login', ['surface' => 'mobile']) }}"
      @endif>
    <div class="mx-auto flex min-h-screen w-full max-w-[440px] items-center px-4 py-[max(1rem,env(safe-area-inset-top))]">
        <div class="mobile-auth-card w-full overflow-hidden rounded-[2rem]">
            @yield('content')
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
