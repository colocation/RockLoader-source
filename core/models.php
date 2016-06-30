<?php 
    require_once( __DIR__ . "/Model.php" );
    
    class ClientModel extends Model {
        public function getClientByID( $app_id ){
            return $this->dbExecute( "select * from client where application_id = " . $app_id );
        }
        
        public function addClient( $app_id, $app_version, $os_id ){            
            $this->dbExecute(
                "insert into client (application_id, application_version, os_id) values ('" .
                $app_id ."'," . $app_version . "," . $os_id .");"
            );
        }
        
        public function getApplicationVersions( ){
            return $this->dbExecute( "select distinct application_version from client" );
        }
        
        public function getClientCount( ){            
            return $this->dbExecute( "select count( id ) as count from client" );
        }
        public function getOnlineClientCount( ){            
            return $this->dbExecute( "select count(id) as count from client_last_daily_request where date(request_date) = CURDATE() and next_request_date > now()" );
        }
        public function getNewClientCount( ){            
            return $this->dbExecute( "select count(id) as count from client where date(add_date) = CURDATE()" );
        }        
        public function getTotalDaily( ){            
            return $this->dbExecute( "select date(add_date) as date, count(id) as count from client where add_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) group by add_date" );
        }
        public function getDailyOnline( ){            
            return $this->dbExecute( "select date, count(date) as count from ( select distinct date(request_date) as date, client_id from client_last_daily_request where request_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) ) a group by date");
        }
        public function getWeekOnline( ){            
            return $this->dbExecute( "select count(client_id) as count from  (select distinct client_id from client_last_daily_request where request_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)) a" );
        }
        public function getMonthOnline( ){            
            return $this->dbExecute( "select count(client_id) as count from  (select distinct client_id from client_last_daily_request where request_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) a" );
        }        
        public function getDailyStatistic( ){            
            return $this->dbExecute( "select date(cl.request_date) as date, os.name as os, c.name as country_id from client_last_daily_request cl, country c, os where cl.country_id = c.id and os.id = cl.os_id and cl.request_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        }
        public function deleteClientStatistic( ){
            return $this->dbExecute( "delete from client_last_daily_request" );
        }    
        public function flushClient( ){
            return $this->dbExecute( "delete from client" );
        }
        
    }    
    $ClientModel = new ClientModel();
    
    
    class TaskModel extends Model {
        public function getAllTask( $extra_sql = '' ){
            $sql = "select * from task";
            
            if( $extra_sql != '' )
                $sql .= ' where ' . $extra_sql;
            return $this->dbExecute( $sql );
        }
        
        public function getTaskForClient( $client_id, $app_version, $country_id ){            
            return $this->getAllTask("is_active = true and task.id not in (select task_id from task_status where client_id = '".
                $client_id ."' ) and ( task.id not in ( select distinct task_id from task_application_version) or task.id in
                ( select distinct task_id from task_application_version where application_version = 0 or application_version = '" .
                $app_version ."')) and task.id in (select task_id from task_country where (country_id = ". $country_id
                ." or country_id = 1) ) limit 1;");
        }
                        
        public function addGivenTime( $task_id ) {
            $this->dbExecute("update task set given_time = given_time + 1 where id = '". $task_id ."';");
        }
        public function deleteTask( $task_id ) {
            return $this->dbExecute("delete from task where id=". $task_id);
        }
        public function getTask( $task_id ) {
            return $this->dbExecute("select * from task where id=". $task_id);
        }
        public function addTask( $name, $is_active, $delete_after_complete ) {
            return $this->dbExecuteReturnID("insert into task (name, is_active, delete_after_finish) values ('".
                $name."',". $is_active .",". $delete_after_complete .")" );
        }
        public function updateTask( $id, $name, $is_active, $delete_after_finish ) {
            $this->dbExecute("update task set name = '". $name ."', is_active = ". $is_active
                .", delete_after_finish = ". $delete_after_finish ." where id = " . $id );
        }
        public function removeTasks( $string ) {
            $this->dbExecute("delete from task where id in (". $string .")");
        }
        public function updateTaskGivenTime( ) {
            $this->dbExecute("update task set given_time = 0");
        }
        
    }    
    $TaskModel = new TaskModel();
    
    class TaskApplicationVersionModel extends Model {
        public function getTaskApplicationVersions( $task_id ) {
            return $this->dbExecute("select * from task_application_version where task_id=". $task_id);
        }
        public function removeTaskApplicationVersions( $task_id ){
            return $this->dbExecute("delete from task_application_version where task_id=". $task_id .";");
        }        
        public function addTaskApplicationVersions( $task_id, $application_versions ){
            $sql_values = "";
      
            foreach( $application_versions as $application_version ) {
              if( strlen( $sql_values ) > 0 )
                $sql_values .= ", ";
                          
              $sql_values .= "(". $task_id . ",". $application_version .")";  
            }
            $sql = "insert into task_application_version (task_id, application_version) values " . $sql_values .";";
            $this->dbExecute( $sql );
        }
    }
    $TaskApplicationVersionModel = new TaskApplicationVersionModel();    
        
    class TaskStatusModel extends Model {
        public function removeStatuses( $task_id, $client_id ) {
            $this->dbExecute("delete from task_status where task_id =". $task_id ." and client_id = ". $client_id );
        }
        public function addStatus( $task_id, $client_id, $status ) {
            $this->dbExecute("insert into task_status (task_id, client_id, status) values (". $task_id .",".
                $client_id .",". $status .");");
        }
        public function getTasksCountByStatus( $status ) {
            return $this->dbExecute( "select task_id, count(status) as count from task_status where status = ". $status." group by `id`" );
        }
        public function getTasksStatusCount( ) {
            return $this->dbExecute( "select id, sum( status ) as 'sum' from ( select a.id, IFNULL(fs.status, 0) as status, fs.client_id  from ( select t.id, f.file_id, f.id as file_internal_id from task t, task_file tf, file f where tf.file_id = f.id and tf.task_id = t.id ) a LEFT OUTER JOIN file_status fs ON a.file_internal_id = fs.file_id and a.id = fs.task_id ) as t group by id" );
        }
        public function flushTasksStatus( ) {
            return $this->dbExecute( "delete from task_status" );
        }
        
    }
    $TaskStatusModel = new TaskStatusModel();
    
    
    class FileStatusModel extends Model {
        //$FileStatusModel->addStatus( $file['file_id'], $task_id, $client['id'], 1 );
        public function addStatus( $file_id, $task_id, $client_id, $status ) {
            return $this->dbExecute( "insert into file_status (file_id, task_id, client_id, status) values (". $file_id .",". $task_id
                .",". $client_id .",". $status .")" );
        }
        public function flushFileStatus( ) {
            return $this->dbExecute( "delete from file_status" );
        }
    }
    $FileStatusModel = new FileStatusModel();
        
    
    class TaskFileModel extends Model {
        public function getFilesForTask( $task_id ){
            return $this->dbExecute( "SELECT f.file_id, f.file_path, f.id, f.encrypted FROM `task_file` tf, `file` f where tf.`file_id`= f.id and tf.task_id = " .
                $task_id . ";");
        }
        public function getFilesAndTasks( ){
            return $this->dbExecute( "select t.id, f.file_id from task t, task_file tf, file f where tf.file_id = f.id and tf.task_id = t.id");
        }
        public function getFilesAndTasksWithStatus(){
            return $this->dbExecute( "select a.id, a.file_id, IFNULL(sum(fs.status), 0) as 'status' from ( select t.id, f.file_id, f.id as file_internal_id from task t, task_file tf, file f where tf.file_id = f.id and tf.task_id = t.id ) a LEFT OUTER JOIN file_status fs ON a.file_internal_id = fs.file_id and a.id = fs.task_id group by id, file_id");
        }
        public function getFiles(){
            return $this->dbExecute( "select * from file; ");
        }
        public function getFile( $file_id ){
            return $this->dbExecute( "select * from file where id=" . $file_id );
        }
        public function updateFile( $file_id, $name, $file_path, $size, $encrypted ){
            return $this->dbExecute( "update file set name='". $name ."', file_path='". $file_path ."', size=".
                $size.", encrypted=". $encrypted ." where id=" . $file_id );
        }
        public function addFile( $name, $file_path, $size, $file_id, $encrypted ){
            return $this->dbExecute( "insert into file (name, file_path, size, file_id, encrypted) values ('". $name ."', '". $file_path ."', ".
                $size.", " . $file_id . ", '". $encrypted. "')" );
        }
        public function removeFile( $file_id ){
            return $this->dbExecute( "delete from file where id=" . $file_id );
        }
        public function removeTaskFiles( $task_id ){
            return $this->dbExecute( "delete from task_file where task_id = ". $task_id);
        }
        public function addTaskFiles( $task_id, $files ){
            $sql_values = "";
      
            foreach( $files as $file ) {
              if( strlen( $sql_values ) > 0 )
                $sql_values .= ", ";
                          
              $sql_values .= "(". $task_id . ",". $file .")";  
            }
            $sql = "insert into task_file (task_id, file_id) values " . $sql_values .";";
            $this->dbExecute( $sql );    
        }
        
        public function getCurrentFileID() {
            return $this->dbExecute("select max(file_id) as file_id from file;");
        }
        public function removeFiles( $string ) {
            $this->dbExecute("delete from file where id in (". $string .")");
        }
    }    
    $TaskFileModel = new TaskFileModel();
    
    
    class RequestModel extends Model {
        public function addRequest( $client_id, $minutes, $country_id, $os_id ){            
            $this->dbExecute( "insert into client_last_daily_request (client_id, next_request_date, country_id, os_id) VALUES (". $client_id
                .", (now() + INTERVAL ". $minutes ." MINUTE), ". $country_id .",". $os_id .")" );
        }
        public function getRequestsOrdered( $client_id ){            
            return $this->dbExecute( "select * from client_last_daily_request where client_id = '".
                $client_id ."' order by request_date desc;");
        }
        public function updateRequest( $request_id, $minutes, $country_id, $os_id ){
            $this->dbExecute( "update client_last_daily_request set request_date = now(), country_id=" . $country_id.", os_id=". $os_id .", next_request_date = now() + INTERVAL ".
                $minutes ." MINUTE where id = " .$request_id );
        }
    }    
    $RequestModel = new RequestModel();
    
    class SettingsModel extends Model {
        public function getSettings (){
            return $this->dbExecute( "select * from settings" );
        }
        public function updateSettings ( $disable_graphics, $delete_after_finish, $ping ){
            return $this->dbExecute( "update settings set disable_graphics =" . $disable_graphics .", command_to_del=" .
                $delete_after_finish . ", ping='" . $ping . "' where id = 1"  );
        }
    }
    $SettingsModel = new SettingsModel();
    
    class UserModel extends Model {
        public function getUser( $username, $password ){            
            return $this->dbExecute( "select * from user where username='" . $username . "' and password='". $password ."';" );
        }
        public function getUserByID( $user_id ){
            return $this->dbExecute( "select * from user where id=" . $user_id );
        }
        public function getUsers( ){
            return $this->dbExecute( "select * from user" );
        }
        public function updateUser( $user_id, $username, $password, $is_admin ){
            return $this->dbExecute( "update user set username='". $username ."', password = '". $password ."', is_admin =".
                $is_admin ." where id =". $user_id );
        }
        public function addUser( $username, $password, $is_admin ){
            return $this->dbExecuteReturnID( "insert into user (username, password, is_admin ) values ('". $username ."', '".
                $password ."', ". $is_admin .");" );
        }
        public function deleteUser( $user_id ){
            $this->dbExecute( "delete from user where id=" . $user_id );
        }
      }    
      $UserModel = new UserModel();
              
      class UserTaskModel extends Model {
        public function getTaskUsers( $task_id ){
            return $this->dbExecute( "select t.*, ut.user_id from user_task ut, task t where t.id = ut.task_id and ut.task_id = ". $task_id );
        }
        public function getUserTasks( $user_id ){
            return $this->dbExecute( "select t.*, ut.user_id from user_task ut, task t where t.id = ut.task_id and ut.user_id = ". $user_id );
        }
        public function getUsersWithTasks(){
            return $this->dbExecute( "select t.*, ut.user_id from user_task ut, task t where t.id = ut.task_id" );
        }
        public function getTasks( ){
            return $this->dbExecute( "select * from task");
        }
        public function getTasksForUser( $user_id ){
            return $this->dbExecute( "select t.* from task t, user_task ut, user u where u.id = ut.user_id and ut.task_id = t.id and u.id = " . $user_id);
        }
        public function removeTaskUsers( $task_id ){
            return $this->dbExecute("delete from user_task where task_id=". $task_id);
        }
        public function removeUserTasks( $user_id ){
            return $this->dbExecute("delete from user_task where user_id=". $user_id);
        }
        public function addTaskUsers( $task_id, $users ){
            $sql_values = "";
      
            foreach( $users as $user ) {
              if( strlen( $sql_values ) > 0 )
                $sql_values .= ", ";
                          
              $sql_values .= "(". $task_id . ",". $user .")";  
            }
            $sql = "insert into user_task (task_id, user_id) values " . $sql_values .";";
            $this->dbExecute( $sql );
        }
        public function addUserTasks( $user_id, $tasks ){
            $sql_values = "";
      
            foreach( $tasks as $task ) {
              if( strlen( $sql_values ) > 0 )
                $sql_values .= ", ";
                          
              $sql_values .= "(". $user_id . ",". $task .")";  
            }
            $sql = "insert into user_task (user_id, task_id) values " . $sql_values .";";
            $this->dbExecute( $sql );
        }
      }    
      $UserTaskModel = new UserTaskModel();
      
      class CountryModel extends Model {
        public function getCountriesTask( $task_id ){
            return $this->dbExecute( "select c.id, c.name from country c, task_country tc where tc.country_id = c.id and tc.task_id = " . $task_id );
        }
        
        public function getCountries( ){
            return $this->dbExecute( "select c.id, c.name from country c" );
        }
        
        public function removeTaskCountries( $task_id ){
            return $this->dbExecute( "delete from task_country where task_id = " . $task_id );
        }
        
        public function addTaskCountries( $task_id, $countries ){
            $sql_values = "";
      
            foreach( $countries as $country ) {
              if( strlen( $sql_values ) > 0 )
                $sql_values .= ", ";
                          
              $sql_values .= "(". $task_id . ",". $country .")";  
            }
            $sql = "insert into task_country (task_id, country_id) values " . $sql_values .";";
            $this->dbExecute( $sql );
        }
        public function getTaskAndCountries( ){
            return $this->dbExecute( "select tc.task_id, tc.country_id, c.name from task_country tc, country c where tc.country_id = c.id");
        }
        public function getCountryByName ( $name ){
            return $this->dbExecute( "select * from country where name='". $name ."'");
        }
        public function addCountry ( $name ){
            return $this->dbExecuteReturnID( "insert into country ( name ) values ('". $name ."');");
        }
        
      }
      $CountryModel = new CountryModel();
      
      
    class OSModel extends Model {
        public function getOSByNumber( $num ){
            return $this->dbExecute( "select id from os where number =" . $num );
        }
        public function getOSByID( $id ){
            return $this->dbExecute( "select * from os where id =" . $id );
        }
        public function getOS( ){
            return $this->dbExecute( "select * from os" );
        }
        public function updateOS( $id, $name, $number ){
            return $this->dbExecute( "update os set name='". $name ."', number=". $number ." where id=". $id  );
        }
        public function addOS( $name, $number ){
            return $this->dbExecuteReturnID( "insert into os ( name, number ) values ('". $name ."', ".
                $number .");" );    
        }
        public function deleteOS( $id ){
            $this->dbExecute( "delete from os where id=". $id );
        }
    }
    $OSModel = new OSModel();
?>