<?php

//function to check if user is loged in
if(!function_exists('isLogedIn')){
    function isLogedIn(){
        return isset ($_SESSION['user_id']);
    }
}

//function to get current user
if(!function_exists('getCurrentUser')){
    function getCurrentUser(){
        if(isLogedIn()){
            return [
                'user_id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'email' => $_SESSION['email'] ?? null,
                'role' => $_SESSION['role'] ?? 'member'
            ];
        }
        return null;
    }
}

// Helper function to sanitize input
if (!function_exists('sanitize')) {
    function sanitize($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

?>
