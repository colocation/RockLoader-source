<?php
  
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    
    $string_to_remove = "";
    
    foreach ( $_POST as $key => $value){
    
      if ( strlen( $string_to_remove ) > 0  )
        $string_to_remove .= ",";
      
      $string_to_remove .= $key;
    }
    
    $TaskModel->removeTasks( $string_to_remove );
        
  }
  
  if ( isAdmin() ) {
    $tasks = $UserTaskModel->getTasks();
  } else {
    $tasks = $UserTaskModel->getTasksForUser( userID() );
  }
  $task_files = $TaskFileModel->getFilesAndTasksWithStatus();
  $task_countries = $CountryModel->getTaskAndCountries();
  $task_statuses = $TaskStatusModel->getTasksStatusCount( );
  $file_result = array();
  
  foreach ( $tasks as $task ) {            
    echo '<tr task_id="'. $task['id'] .'">';
    echo '<td><input type="checkbox" name="'. $task['id'] .'"></td>';
      //echo "<td>".  ."</td>";
      echo '<td><a href="/cp/task?id='. $task['id'] .'">'.$task['name']."</a></td>";
      
      // Countries
      echo "<td>";
      $task_row = '';      
      foreach ( $task_countries as $tc ) {
        if ( $tc["task_id"] == $task["id"] ) {
          if ( is_integer( $task_row ) ) {
            $task_row ++;
          } else {
            if( strlen( $task_row ) > 0 )
              $task_row = 2;
            else
              $task_row = $tc['name'];
          }          
        } 
      }      
      echo $task_row . "</td>";
      
      // Files
      echo "<td>";
      $task_row = '';
            
      foreach ( $task_files as $task_file ) {
        
        if ( $task_file["id"] == $task["id"] ) {
          
          if ( strlen( $task_row ) > 0 )
            $task_row .= "; ";
            
          $task_row .= '<a href="/cp/file?id='.$task_file['file_id'].'">'. $task_file['file_id'] . "</a>";
          
          $file_result_row = array(
            "task_id" => $task['id']
          );
          $file_result_row['file_id'] = $task_file['file_id'];
          $file_result_row['file_name'] = "Файл " . $task_file['file_id'];
          $file_result_row['status'] = $task_file['status'];
          $file_result[] = $file_result_row;
        } 
      }      
      echo $task_row . "</td>";
      
      //Given time
      echo "<td>". $task['given_time']."</td>";
      
      // Completed      
      $count = 0;

      foreach ( $task_statuses as $task_status ) {
        if ( $task_status['id'] == $task['id'] )
          $count = $task_status['sum'];
      }
      
      echo '<td><a href="#" class="task-result-toggle">'. $count .'</a></td>';
          
    echo "</tr>";
  }
  
  
?>