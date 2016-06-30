<?php
    
    require_once( __DIR__ . "/../init.php" );
    
    define("STATIC_PATH", "/cp/");
    
    //Auth
    session_start();
    if( empty($_SESSION["authenticated"]) || $_SESSION["authenticated"] != 'true' )
        if ( $_SERVER['REQUEST_URI'] != "/cp/login" && $_SERVER['REQUEST_URI'] != "/cp/login/" ) {
            redirect( 'Location: /cp/login' );    
        }
    
    function isAdmin () {
        if ( isset( $_SESSION["user_is_admin"] ) )
            if ( $_SESSION["user_is_admin"] )
                return true;
        return false;    
    }
    
    function userID () {
        if ( isset( $_SESSION["user_id"] ) )
             return $_SESSION["user_id"];
        return false;            
    }
    
?>

