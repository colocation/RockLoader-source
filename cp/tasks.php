<?php
  require_once( __DIR__ . "/init.php");
  header("Content-Type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/base.css" rel="stylesheet">
    
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
      <div class="container">
      <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Панель управления</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="login/logout.php">Выход</a></li>
          </ul>
        </div>
      </div>
    </nav>        
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <?php if ( isAdmin() ): ?>
              <li><a href="/cp/">Статистика</a></li>
            <?php endif; ?>            
            <li class="active"><a href="tasks">Задачи<span class="sr-only">(current)</span></a></li>
            <?php if ( isAdmin() ): ?>
              <li><a href="users">Пользователи</a></li>
              <li><a href="files">Файлы</a></li>
              <li><a href="settings">Настройки</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h2 class="sub-header">
            Задачи
            <?php if ( isAdmin() ): ?>
            <a id="remove-tasks" disabled="disabled" class="btn btn-danger float-right" role="button">Удалить задачи</a>
            <a href="/cp/task" class="btn btn-info float-right" role="button">Новая задача</a>
            <?php endif; ?>            
          </h2>
          <div class="table-responsive">
            <form method="post">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th></th>
                    <!--th>#</th-->
                    <th>Название</th>
                    <th>Страны</th>
                    <th>Файлы</th>
                    <th>Взяли задание</th>
                    <th>Успешно выполнено</th>
                  </tr>
                </thead>
                <tbody>
                  <?php require_once( __DIR__ . "/controller/tasks.php"); ?>
                </tbody>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>

    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
      $( document ).ready( function(){
                  
          var fileStatistic = <?php echo json_encode( $file_result ); ?>,
            active_task_statistic_id = 0
          ;          
          
          console.log( fileStatistic );
          
          $( ".task-result-toggle" ).on( 'click', function() {
            
            // toggle off previous statistics            
            $( ".task-status-row" ).remove();            
            
            // check if current file statistics is shown
            var task_id = $(this).parent().parent().attr('task_id');
            
            if ( task_id == active_task_statistic_id ) {
              
              // disable statistics
              active_task_statistic_id = 0
              
            } else {
            
              // enable new statistics
              for ( var i in fileStatistic ) {
                     
                var task = fileStatistic[i];
                
                if ( task['task_id'] == task_id ) {
                  
                  var tr_temp = $( '<tr class="task-status-row"><td></td><td></td><td></td></tr>' );
                  
                  $( "<td>"+ task['file_name'] +"</td><td></td><td>"+ task['status'] +"</td>" ).appendTo( tr_temp );                                  
                  tr_temp.insertAfter( $(this).parent().parent() );
                                    
                }              
              }
              
              active_task_statistic_id = task_id;
            }
            
          });
          
          var task_ids = [];
          
          $( "table input" ).on( 'change', function() {
            
            task_ids = [];
                                    
            if ( $( 'input[type=checkbox]:checked' ).length > 0 ) {
              $( "#remove-tasks" ).attr( "disabled", null );
            } else {
              $( "#remove-tasks" ).attr( "disabled", "disabled" );
            }
            
            $( 'input[type=checkbox]:checked' ).each( function () {
              
              task_ids.push( $( this ).parent().next().text() );
            });
                        
          });
          
          $( "#remove-tasks" ).on( 'click', function () {
            if ( $( "#remove-tasks").attr("disabled") == "disabled" ) {
              return;
            }
            if ( confirm( 'Вы точно хотите удалить выбранные задачи?' ) ) {
              $( "form" ).submit();
            }

          });
          
      });
    </script>
  </body>
</html>
