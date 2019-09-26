<?php
session_start();

// Fix for removed Session functions 
function fix_session_register(){ 
    function session_register(){ 
        $args = func_get_args(); 
        foreach ($args as $key){ 
            $_SESSION[$key]=$GLOBALS[$key]; 
        } 
    } 
    function session_is_registered($key){ 
        return isset($_SESSION[$key]); 
    } 
    function session_unregister($key){ 
        unset($_SESSION[$key]); 
    } 
} 
if (!function_exists('session_register')) fix_session_register(); 

	session_register("version");
	$_SESSION['version'] = '2.0.0';
?>
