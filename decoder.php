<?php
    
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

    function decode( $src ) { 
        $ret = ''; 
        for ( $n = 1; $n < strlen( $src ); $n += 2) { 
            $input = ord( $src[ $n ] ); 
            $F = $input & 0x0F; 
            $E = $input >> 4; 
            $D = ord( $src[ $n-1] ) & 0x0F; 
            $ret .= chr( ( $D^$E )*16 + $F ); 
        } 
        return $ret; 
    }  

    function encode( $src ) { 
        $dest = ''; 
        for ( $n = 0; $n < strlen( $src ); $n++ ) { 
            $input = ord( $src[ $n ] ); 
            $A = $input >> 4; 
            $F = $B = $input&0x0F; 
            $E = mt_rand( 3, 7 ); 
            $D = $A^$E; 
            $C = mt_rand( 3,7 ); 
            $dest .= chr( $C*16 + $D ).chr( $E*16 + $F ); 
        } 
        return $dest.'A'; 
    }
    
    if( isset( $_POST[ 'encode' ] ) )
        $data = encode( $_POST['data'] );
    else
        $data = decode( $_POST['data'] );

} else {
    $data = '';
}

?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title> 
  </head>
    <body>
        <form method='post'>
            <textarea rows="10" cols="45" name="data" value=""><?php echo $data ?></textarea>
            <input type="submit" value="Encode" name="encode">
            <input type="submit" value="Decode" name="decode">
        </form>    
    </body>
</html>
