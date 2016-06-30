<?php    
  if ( ! isAdmin() )
    redirect( 'Location: /cp/tasks' );
    
  $new_user = false;
  $user_tasks = array();
  
  // Try get task
  $user = false;
  $tasks = $UserTaskModel->getTasks();
  
  if ( isset( $_GET['id'] ) ){
    $user_result = $UserModel->getUserByID( $_GET['id'] );
      if ( count ( $user_result ) > 0 )
        $user = $user_result[0];
  }
    
  // POST
  
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            
    if( $user ){
      
      // delete exist user
      
      if( isset( $_POST['delete'] ) ){        
        $UserModel->deleteUser( $user['id'] );
        redirect( 'Location: /cp/users' );
      }
      
      if( ! isset( $_POST['password'] ) )
        $_POST['password'] = '';
      
      if( $user['password'] != $_POST['password'] ) {
        $password = create_hash( $_POST['password'] );
      } else {
        $password = $_POST['password'];
      }
      
      // update exist user      
      $UserModel->updateUser(
        $user['id'],
        isset( $_POST['username'] ) ? $_POST['username'] : '',
        $password,
        isset( $_POST['is_admin'] ) ? 1 : 0
      );
      
    } else {
      //create new user
      $user['id'] = $UserModel->addUser(
        isset( $_POST['username'] ) ? $_POST['username'] : '',
        isset( $_POST['password'] ) ? create_hash( $_POST['password'] ) : create_hash( '' ),
        isset( $_POST['is_admin'] ) ? 1 : 0
      );
      
      print_r( $user['id'] );
    }
    
        
    if( isset( $_POST["tasks"] ) ) {      
      $UserTaskModel->removeUserTasks( $user['id'] );      
      $UserTaskModel->addUserTasks( $user['id'], $_POST['tasks'] );
    }
          
    redirect( 'Location: /cp/users' );
  }
  
  
  // GET
  if ( isset( $_GET['id'] ) ) {
        
    if( ! $user )
      redirect( 'Location: /cp/users' );
       
    $user_tasks = $UserTaskModel->getUserTasks( $user['id'] );    
    return;
  } 
  
  // Create new task
  $new_user = true;
    
?>
