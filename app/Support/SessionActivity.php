<?php

namespace App\Support;

use Illuminate\Http\Request;

class SessionActivity
{
    public const LAST_ACTIVITY_KEY = 'last_activity_at';

    public static function timeoutMinutes(): int
    {
        return max(1, (int) config('session.idle_timeout', 30));
    }

    public static function heartbeatSeconds(): int
    {
        return max(15, (int) config('session.idle_heartbeat_seconds', 60));
    }

    public static function timeoutMilliseconds(): int
    {
        return self::timeoutMinutes() * 60 * 1000;
    }

    public static function isExpired(Request $request): bool
    {
        $lastActivity = (int) $request->session()->get(self::LAST_ACTIVITY_KEY, 0);
        if ($lastActivity <= 0) {
            return false;
        }

        return (time() - $lastActivity) >= (self::timeoutMinutes() * 60);
    }

    public static function touch(Request $request): void
    {
        $request->session()->put(self::LAST_ACTIVITY_KEY, time());
    }

    public static function clearAuthenticatedState(Request $request): void
    {
        $surface = ClientSurface::current($request);

        $request->session()->forget([
            'id',
            'role',
            'platform',
            self::LAST_ACTIVITY_KEY,
        ]);

        ClientSurface::remember($request, $surface);
    }
}
