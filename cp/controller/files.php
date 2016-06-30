<?php
  if ( ! isAdmin() )
    header('Location: /cp/tasks');
    
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    
    $string_to_remove = "";
    
    foreach ( $_POST as $key => $value){
    
      if ( strlen( $string_to_remove ) > 0  )
        $string_to_remove .= ",";
      
      $string_to_remove .= $key;
    }
    
    $TaskFileModel->removeFiles( $string_to_remove );
        
  }
  
  $files = $TaskFileModel->getFiles(); 
  
  foreach ( $files as $file ) {
    echo "<tr>";
      echo '<td><input type="checkbox" name="'. $file['id'] .'"></td>';
      echo '<td><a href="/cp/file?id='. $file['id'] .'">'.$file['name']."</a></td>";
            
      // Date add
      echo "<td>". $file['add_date']."</td>";
                  
      // Size
      echo "<td>". $file['size']." B</td>";
      
    echo "</tr>";
  }


?>