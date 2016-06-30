<?php require_once( "init.php"); ?>
<?php require_once( "controller/settings.php"); ?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Настройки</title>

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
              <li><a href="tasks">Задачи</a></li>
              <li><a href="users">Пользователи</a></li>
              <li><a href="files">Файлы</a></li>
              <li class="active"><a href="settings">Настройки <span class="sr-only">(current)</span></a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">          
          <h2 class="sub-header">Настройки</h2>                  
          <form method="post">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="disable_graphics" <?php if ( $settings["disable_graphics"] ) echo "checked"; ?> > Отключить графики
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="delete_after_finish" <?php if ( $settings["command_to_del"] ) echo "checked"; ?> > Самоудаление
              </label>
            </div>
            <fieldset class="form-group">
              <label for="inputOut">Таймаут</label>
              <div class="input-group">              
                <input type="text" id="inputOut" class="form-control" name="ping" value="" placeholder="<?php echo $settings["ping"]; ?>" aria-describedby="basic-addon2">
                <span class="input-group-addon" id="basic-addon2">минут</span>
              </div>
            </fieldset>
            <fieldset class="form-group">
              <label for="inputOut">Удаление статистики</label>
              <div class="input-group">              
                  <div class="checkbox">
                <label>
                  <input type="checkbox" name="delete_main_statistics" > Удаление главной статистики  
                </label>
              </div>
              <div class="input-group">              
                  <div class="checkbox">
                <label>
                  <input type="checkbox" name="delete_task_statistics" > Удаление статистики задач и файлов 
                </label>
              </div>
              </div>
            </fieldset>
            <button type="submit" name="save" class="btn btn-primary">Сохранить</button>
          </form>
        </div>
      </div>
    </div>

    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
