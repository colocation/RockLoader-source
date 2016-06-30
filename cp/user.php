<?php require_once( __DIR__ . "/init.php"); ?>
<?php include_once( __DIR__ . "/controller/user.php"); ?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Пользователь</title>

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
              <li class="active"><a href="users">Пользователи<span class="sr-only">(current)</span></a></li>
              <li><a href="files">Файлы</a></li>
              <li><a href="settings">Настройки</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h2 class="sub-header">
            <?php if( $new_user ) echo "Новый пользователь"; else echo "Пользователь: " . $user["username"]; ?>
          </h2>
          <form method="post" autocomplete="off">
            <fieldset class="form-group">
              <input style="display:none">
              <label for="inputName">Имя пользователя</label>
              <input class="hidden" name="username" id="fake_username" autocomplete="off" >
              <input class="form-control" name="username" id="inputName" autocomplete="off"
                placeholder="Логин" value="<?php if( $new_user ) echo "Пользователь"; else echo $user["username"]; ?>">
            </fieldset>
            <fieldset class="form-group">              
              <label for="inputPassord">Пароль</label>              
              <input type="password" class="form-control" name="password" id="inputPassord" autocomplete="off"
                  placeholder="Пароль" value="<?php echo $user["password"];?>">
            </fieldset>
            <fieldset class="form-group">
              <label for="taskSelect">Список доступных для просмотра задач</label>
              <select size="9" multiple class="form-control" name="tasks[]" id="taskSelect">
                <?php
                  foreach( $tasks as $task ){
                    echo '<option ';
                    foreach( $user_tasks as $user_task )
                      if( $user_task['id'] == $task['id'] )
                        echo "selected ";
                    echo 'value='.$task["id"].'>'. $task["name"] ."</option>";
                  }
                ?>                
              </select>
            </fieldset>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="is_admin" <?php if ( $user["is_admin"] ) echo "checked"; ?> > Администратор
              </label>
            </div>
            <button type="submit" name="save" class="btn btn-primary">Сохранить</button>
            <?php if ( ! $new_user ): ?>
              <button type="submit" name="delete" class="btn btn-danger float-right">Удалить</button>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </div>

    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js">
      $(document).ready( function () {
        $("#inputName").attr("autocomplete", false);
      });
    </script>
  </body>
</html>
