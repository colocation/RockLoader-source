<?php

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
    
$server_url = "http://159.203.90.151/api/";

$test_data = array(
    "ID1"=> "1234567893",
    "ID2"=> "1234567890",
    "ID3"=> "4762356870",
    "ID4"=> "3232946318",
    "time"=> "1435093393 ",
    "type"=> "getjob",
    "customField"=> array(
        "Dat1"=> "123",
        "Dat2"=> "Test"
    ),
    "Accepte"=>2,
    "Completed1" => 2,
    "Completed2" => array(
        "url12",
        "url13"
    ),
);

$test_data = json_encode($test_data);
$test_data = encode($test_data);
$test_data = array('data' => $test_data );

$opts = array(
    'http'=> array(
        'method'=>"POST",
        'header'=>"Accept-language: en\r\n" . "Cookie: foo=bar\r\n",
        'content' => http_build_query($test_data)
        //'content' => $test_data
    )
);

//$context = stream_context_create( $opts );

//print_r( $context );
//echo file_get_contents( $server_url, false, $context );

?>
