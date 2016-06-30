<?php require_once( __DIR__ ."/init.php"); ?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Файлы</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/base.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
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
            <li><a href="/cp/">Статистика</a></li>
            <?php if ( isAdmin() ): ?>
              <li><a href="tasks">Задачи</span></a></li>
              <li><a href="users">Пользователи</a></li>
              <li class="active"><a href="files">Файлы<span class="sr-only">(current)</a></li>
              <li><a href="settings">Настройки</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h2 class="sub-header">
            Файлы
            <a id="remove-files" disabled="disabled" class="btn btn-danger float-right" role="button">Удалить файлы</a>
            <a href="/cp/file" class="btn btn-info float-right" role="button">Новый файл</a>
          </h2>
          <div class="table-responsive">
            <form method="post">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th></th>
                  <th>Имя файла</th>
                  <th>Дата добавления</th>
                  <th>Размер</th>                  
                </tr>
              </thead>
              <tbody>
                <?php require_once( __DIR__ ."/controller/files.php"); ?>
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
      $( document ).ready( function () {
        var file_ids = [];
          
        $( "table input" ).on( 'change', function() {
          
          file_ids = [];
                                  
          if ( $( 'input[type=checkbox]:checked' ).length > 0 ) {
            $( "#remove-files" ).attr( "disabled", null );
          } else {
            $( "#remove-files" ).attr( "disabled", "disabled" );
          }
          
          $( 'input[type=checkbox]:checked' ).each( function () {
            
            file_ids.push( $( this ).attr('name') );
          });
                      
        });
        
        $( "#remove-files" ).on( 'click', function () {
          if ( $( "#remove-files").attr("disabled") == "disabled" ) {
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
