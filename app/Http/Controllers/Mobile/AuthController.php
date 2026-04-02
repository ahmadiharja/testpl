<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Support\ClientSurface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    protected function loadSettings(): array
    {
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return Setting::pluck('value', 'title')->toArray();
    }

    protected function resolveUser(Request $request): ?User
    {
        $userId = $request->session()->get('id');

        return $userId ? User::find($userId) : null;
    }

    public function login(Request $request)
    {
        ClientSurface::remember($request, ClientSurface::MOBILE);

        if ($this->resolveUser($request)) {
            return redirect()->route('mobile.dashboard');
        }

        return view('mobile.auth.login', [
            'title' => 'Mobile Login',
            'settings' => $this->loadSettings(),
        ]);
    }

    public function choosePlatform(Request $request)
    {
        ClientSurface::remember($request, ClientSurface::MOBILE);
        $user = $this->resolveUser($request);
        if (!$user) {
            return redirect()->route('mobile.login');
        }

        if ($user->platform !== 'both') {
            $request->session()->put('platform', $user->platform === 'perfectchroma' ? 'perfectchroma' : 'perfectlum');

            return redirect()->route('mobile.dashboard');
        }

        return view('mobile.auth.choose-platform', [
            'title' => 'Choose Platform',
            'settings' => $this->loadSettings(),
            'mobileUser' => $user,
            'idleLogoutEnabled' => true,
        ]);
    }

    public function selectPlatform(Request $request, string $platform)
    {
        ClientSurface::remember($request, ClientSurface::MOBILE);
        $user = $this->resolveUser($request);
        if (!$user) {
            return redirect()->route('mobile.login');
        }

        if (in_array($platform, ['perfectlum', 'perfectchroma'], true)) {
            $request->session()->put('platform', $platform);
        }

        return redirect()->route('mobile.dashboard');
    }
}
