<?php
    function ErrorReturn ( $error_string ) {
        if ( ! isset( $error_string ) )
            $error_string = "You have hot an error. Something wrong.";
        echo $error_string . "\n";    
        exit(); 
    }
    
    function redirect ( $url ) {
        header( $url );
        exit();
    }
    
    function create_hash( $string ) {
        return substr( sha1( SALT . $string ), 3, 17 );
    }
    
    function encodeResponse ( $response_array ){
        return  base64_encode(  json_encode( $response_array ) );        
    }
    
    function xorFile( $filename, $key ) {
    
        $reading = fopen($filename, 'r');
        $writing = fopen($filename.'.tmp', 'w');        
        
        $j = 0;
        
        while ( ! feof( $reading ) ) {
            $text = fgets( $reading );
            $outText = '';            
            for( $i = 0; $i < strlen( $text ); $i++ ) {                
                $outText .= $text{$i} ^ $key{$j};
                $j++;
                if( $j == strlen( $key ) )
                    $j = 0;
            }
            
                        
            fputs($writing, $outText);        
        }
    
        fclose($reading);
        fclose($writing);
            
        rename( $filename.'.tmp', $filename );
    }
?>

