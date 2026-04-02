<?php

namespace App\Support;

use Illuminate\Http\Request;

class ClientSurface
{
    public const MOBILE = 'mobile';
    public const DESKTOP = 'desktop';
    public const SESSION_KEY = 'preferred_surface';

    public static function normalize(?string $surface): ?string
    {
        return in_array($surface, [self::MOBILE, self::DESKTOP], true) ? $surface : null;
    }

    public static function detectFromRequest(Request $request): string
    {
        $header = $request->header('Sec-CH-UA-Mobile');
        if ($header === '?1') {
            return self::MOBILE;
        }

        $userAgent = strtolower((string) $request->userAgent());
        if ($userAgent !== '' && preg_match('/android|iphone|ipad|ipod|iemobile|opera mini|blackberry|mobile/', $userAgent)) {
            return self::MOBILE;
        }

        return self::DESKTOP;
    }

    public static function current(Request $request, bool $allowSession = true): string
    {
        $explicit = self::normalize($request->query('surface'));
        if ($explicit) {
            return $explicit;
        }

        if ($allowSession) {
            $sessionSurface = self::normalize($request->session()->get(self::SESSION_KEY));
            if ($sessionSurface) {
                return $sessionSurface;
            }
        }

        return self::detectFromRequest($request);
    }

    public static function remember(Request $request, ?string $surface = null): string
    {
        $resolved = self::normalize($surface) ?? self::current($request, false);
        $request->session()->put(self::SESSION_KEY, $resolved);

        return $resolved;
    }

    public static function loginUrl(Request $request): string
    {
        return self::current($request) === self::MOBILE
            ? route('mobile.login', ['surface' => self::MOBILE])
            : url('login?surface=' . self::DESKTOP);
    }

    public static function dashboardUrl(Request $request): string
    {
        return self::current($request) === self::MOBILE
            ? route('mobile.dashboard')
            : url('dashboard');
    }

    public static function choosePlatformUrl(Request $request): string
    {
        return self::current($request) === self::MOBILE
            ? route('mobile.choose-platform')
            : url('choose-platform');
    }
}
