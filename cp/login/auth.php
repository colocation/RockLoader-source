
<?php    
    
    require_once( __DIR__ . "/../init.php");    
    
    $username = null;
    $password = null;
        
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
        $USER_IS_ADMIN = false;
        $_SESSION["authenticated"] = false;
            
        
        if( !empty( $_POST["username"]) && !empty($_POST["password"] ) ) {
            $username = $_POST["username"];
            $password = create_hash( $_POST["password"] );                
            
            $user_result = $UserModel->getUser( $username, $password );                        
            
            if( count( $user_result ) > 0 ) {
                                
                session_start();
                $_SESSION["authenticated"] = 'true';
                $_SESSION["user_id"] = $user_result[0]['id'];
                
                if( $user_result[0]['is_admin'] )
                    $_SESSION["user_is_admin"] = 'true';
    
                redirect( 'Location: /cp/' );                
    
            } else {
                redirect( 'Location: /cp/login' );
            }
    
        } else {
            redirect( 'Location: /cp/login' );
        }
    } 
?>