<?php
//  Session initialization file

//  Start session if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    //  Strengthen session security
    ini_set('session.use_strict_mode', '1');   // Prevent session fixation
    ini_set('session.cookie_httponly', '1');   // Protect cookies from JavaScript access

    //  Start session
    session_start();
}
