<?php
  if ( ! isAdmin() )
    redirect( 'Location: /cp/tasks' );
    
  // POST
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
          
    $SettingsModel->updateSettings(      
      isset( $_POST['disable_graphics'] ) ? 1 : 0,
      isset( $_POST['delete_after_finish'] ) ? 1 : 0,
      isset( $_POST['ping'] ) ? $_POST['ping']  : 5
    );
    
    //  удаление статистики    
    if( isset( $_POST['delete_main_statistics'] ) ){
      $ClientModel->deleteClientStatistic();
      $ClientModel->flushClient();
    }
        
    if( isset( $_POST['delete_task_statistics'] ) ) {
      $TaskStatusModel->flushTasksStatus();
      $TaskModel->updateTaskGivenTime();
      $FileStatusModel->flushFileStatus();      
    }
    
    
    redirect( 'Location: /cp' );
  }
  
  $settings = $SettingsModel->getSettings();
  
  if( count( $settings ) > 0 )
    $settings = $settings[0];
    
  
?>