<?php
  if ( ! isAdmin() ){
    redirect( 'Location: /cp/tasks' );    
  }
  
  $countries = $CountryModel->getCountries();
  $files = $TaskFileModel->getFiles();
  $users = $UserModel->getUsers();  
  $application_versions = $ClientModel->getApplicationVersions();
  $new_task = false;
  
  // Try get task
  $task = false;
  
  if ( isset( $_GET['id'] ) ){
    $task_result = $TaskModel->getTask( $_GET['id'] );
      if ( count ( $task_result ) > 0 )
        $task = $task_result[0];
  }
    
  // POST
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    
    $new_task = array(
      "name" => $_POST['name'],
      "is_active" => isset( $_POST['is_active'] ) ? 1 : 0,
      "delete_after_finish" => isset( $_POST['delete_after_finish'] ) ? 1 : 0,
    );
        
    if( $task ){
      
      // delete exist task      
      if( isset( $_POST['delete'] ) ){        
        $TaskModel->deleteTask( $task['id'] );
        redirect( 'Location: /cp/tasks' );
      }
      
      // update exist task      
      $TaskModel->updateTask( $task['id'], $new_task["name"], $new_task["is_active"], $new_task["delete_after_finish"] );
      $new_task['id'] = $task['id'];      
    } else {
      //create new tast
      $new_task['id'] = $TaskModel->addTask( $new_task["name"], $new_task["is_active"], $new_task["delete_after_finish"] );
    }
    
    if( isset( $_POST["users"] ) ){      
      $UserTaskModel->removeTaskUsers( $new_task['id'] );      
      $UserTaskModel->addTaskUsers( $new_task['id'], $_POST['users'] );               
    }
    
    if( isset( $_POST["files"] ) ){
      $TaskFileModel->removeTaskFiles( $new_task['id'] );      
      $TaskFileModel->addTaskFiles( $new_task['id'], $_POST['files'] );
    }
    
    if( isset( $_POST["countries"] ) ){
      $CountryModel->removeTaskCountries( $new_task['id'] );      
      $CountryModel->addTaskCountries( $new_task['id'], $_POST['countries'] );
    }
    
    if( isset( $_POST["application_versions"] ) ){
      $TaskApplicationVersionModel->removeTaskApplicationVersions( $new_task['id'] );      
      $TaskApplicationVersionModel->addTaskApplicationVersions( $new_task['id'], $_POST['application_versions'] );
    }
    
    redirect( 'Location: /cp/tasks' );
  }
  
  // GET
  if ( isset( $_GET['id'] ) ) {
        
    if( ! $task )
      redirect( 'Location: /cp/tasks' );
       
    $task["countries"] = $CountryModel->getCountriesTask( $task['id'] );
    $task["files"] = $TaskFileModel->getFilesForTask( $task['id'] );
    $task["users"] = $UserTaskModel->getTaskUsers( $task['id'] );    
    $task["application_versions"] = $TaskApplicationVersionModel->getTaskApplicationVersions( $task['id'] );
    
    return;
  } 
  
  // Create new task
  $new_task = true;
  $task = array(
    "name" => "Новая задача",
    "is_active" => 1,
    "delete_after_finish" => 0,
    "countries" => array(),
    "files" => array(),
    "users" => array(),
    "application_version" => array()
  );
?>
