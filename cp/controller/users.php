<?php
  if ( ! isAdmin() )
    redirect( 'Location: /cp/tasks' );
    
  $users = $UserModel->getUsers();
  $tasks = $UserTaskModel->getUsersWithTasks();
  
  foreach ( $users as $user ) {
    echo "<tr>";      
    
    echo '<td><a href="/cp/user?id='. $user['id'] .'">'.$user['username']."</a></td>";
            
    // Tasks
    echo "<td>";
    foreach ( $tasks as $task ) {
      if ( $task['user_id'] == $user['id'] ) {
        echo '<a href="/cp/task?id='. $task['id'] .'">'. $task['name'] .'</a><br>';              
      }
    }
    echo "</td>";
        
    echo "</tr>";
  }


?>