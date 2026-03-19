<?php

namespace App\Helpers;

use App\Models\User;

class AuthHelper
{
    /**
     * Get current authenticated user from session
     * This app uses custom session-based authentication
     * 
     * @return User|null
     */
    public static function getCurrentUser(): ?User
    {
        $userId = session('id');
        
        if (!$userId) {
            return null;
        }
        
        return User::find($userId);
    }
    
    /**
     * Get current user's role
     * 
     * @return string
     */
    public static function getCurrentUserRole(): string
    {
        return session('role', 'user');
    }
}
