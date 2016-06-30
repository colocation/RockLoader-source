<?php
  if ( ! isAdmin() )
    redirect( 'Location: /cp/tasks' );
  
  function generateRandomString($length = 6) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
  }

  // Try get file
  $file = false;
    
  if ( isset( $_GET['id'] ) ){
    $file_result = $TaskFileModel->getFile( $_GET['id'] );
    if ( count ( $file_result ) > 0 )
      $file = $file_result[0];          
  }
  
  // POST
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

    $file_name = '';
    $file_size = 0;
    
    // load file from URL or save URL to database
    if ( isset( $_POST['url'] ) ) {
      
      if ( isset( $_POST['dynamic'] ) ) {
                
        // save URL to database        
        $file_size = 0;
        $file_name = $_POST['url'];
        
      } else {
        
        // load file from URL 
        $pi = pathinfo( $_POST['url'] );
        
        if ( isset( $pi['extension'] ) ){
          $file_name = generateRandomString() .".". $pi['extension'];      
          file_put_contents( FILE_PATH . $file_name , fopen( $_POST['url'], 'r') );
          
          chmod( FILE_PATH . $file_name, 0777 );
          
          if( isset( $_POST['xor'] ) )
            xorFile( FILE_PATH . $file_name, XOR_KEY );
            
          $file_size = filesize( FILE_PATH . $file_name );      
        }
      }
    }
    
    //load file and get size and path
    if ( is_uploaded_file( $_FILES['file']['tmp_name'] ) ) {
      
      unset( $_POST['dynamic'] );
      
      $info = pathinfo( $_FILES['file']['name'] );
      $ext = $info['extension']; 
      $file_name = generateRandomString() .".". $ext; 
                  
      move_uploaded_file( $_FILES['file']['tmp_name'], FILE_PATH . $file_name );
      
      chmod( FILE_PATH . $file_name, 0777 );
      
      if( isset( $_POST['xor'] ) )
        xorFile( FILE_PATH . $file_name, XOR_KEY );
        
      $file_size = filesize( FILE_PATH . $file_name );      
    }
    
    $encrypted = false;
    if( isset( $_POST['xor'] ) && ! isset( $_POST['dynamic'] ) )
      $encrypted = true;
    
    if( $file ) {      
                    
      //delete old file if it need
      if( strlen( $file_name ) > 0 ){        
        if( file_exists( FILE_PATH . $file['file_path'] ) ){          
          unlink( FILE_PATH . $file['file_path'] );
        }
      }
            
      if( isset( $_POST['delete'] ) ){
        // delete current file
        $TaskFileModel->removeFile( $_GET['id'] );                
      } else {
        // update current file        
        $TaskFileModel->updateFile( $_GET['id'], $_POST['name'], $file_name, $file_size, $encrypted );
      }
      
    } else {
      
      // add new file to database
      $file_id_result = $TaskFileModel->getCurrentFileID();
      
      $file_id = 1;
      if( count( $file_id_result ) > 0 )
        $file_id = $file_id_result[0]['file_id'];
      
      if( $file_id >= 99 )
        $file_id = 1;
        
      $TaskFileModel->addFile( $_POST['name'], $file_name, $file_size, ++$file_id, $encrypted );
    }
    
    redirect( 'Location: /cp/files' );        
  }
  
  // GET
  if ( isset( $_GET['id'] ) ){
    
    if( ! $file )
      redirect( 'Location: /cp/files' );
  }
  
?>
