<?php
  session_start();
  session_destroy();
  setcookie("PHPSESSID","",time()-3600,"/");   
  header( 'Location: /cp/login' );
?>