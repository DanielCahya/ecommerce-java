<?php
class ImplicitUserRules {
    
    // Apply implicit rules to user data
    public static function applyRules($user) {
        $user['status'] = self::getUserStatus($user);
        
        $user['level'] = self::getUserLevel($user);
        
        $user['profile_completion'] = self::calculateProfileCompletion($user);
        
        $user['verified'] = self::isVerified($user);
        
        return $user;
    }

    // Get user status based on ID

    private static function getUserStatus($user) {
        if (isset($user['id']) && $user['id'] < 5) {
            return 'VIP';
        }
        return 'Regular';
    }

    // Calculate user level
    private static function getUserLevel($user) {
        $userId = $user['id'] ?? 0;
        
        if ($userId < 3) return 'Gold';
        if ($userId < 10) return 'Silver';
        return 'Bronze';
    }

    // Calculate profile completion percentage
    private static function calculateProfileCompletion($user) {
        $fields = ['name', 'email', 'password'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user[$field])) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }

    // Check if user is verified
    private static function isVerified($user) {
        if (!empty($user['email']) && filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
}