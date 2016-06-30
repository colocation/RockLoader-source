<?php
    class Model {
        public function dbExecuteReturnID( $sql ){
            return $this->dbExecute( $sql, 1 );        
        }
        
        public function dbExecute( $sql, $return_id = 0 ){
            
            $mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('Unable to connect to database:' . mysql_error());
                        
            if ($mysqli->connect_errno) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
            $mysqli->set_charset("utf8");
            //echo $sql;
            
            $result = $mysqli->query($sql) or die('Could not complete query: ' . mysql_error());                    
            $rows = array();
                
            if( ! is_bool($result) ) {
                if( mysqli_num_rows($result) > 0 ){
                                        
                    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
                        $rows[] = $row;
                    }                    
                    $result->free();
                }
            } else {
                if( $return_id )
                    $rows = mysqli_insert_id( $mysqli );                
            }
                            
            $mysqli->close();
            
            return $rows;
        }
    }
?>

