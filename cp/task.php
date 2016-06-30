<?php include_once( __DIR__ . "/init.php"); ?>
<?php include_once( __DIR__ . "/controller/task.php"); ?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title></title>

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
              <li class="active"><a href="tasks">Задачи<span class="sr-only">(current)</span></a></li>
              <li><a href="users">Пользователи</a></li>
              <li><a href="files">Файлы</a></li>
              <li><a href="settings">Настройки</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">          
          <h2 class="sub-header">Задача: <?php echo $task["name"];?> </h2>
          <form method="post" enctype='multipart/form-data'>
            <fieldset class="form-group">              
              <label for="inputName">Название</label>              
              <input class="form-control" name="name" id="inputName" placeholder="Введите название" value="<?php echo $task["name"];?>">
            </fieldset>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="is_active" <?php if ( $task["is_active"] ) echo "checked"; ?> > Включена
              </label>
            </div>
            <div class="row">              
              <fieldset class="form-group col-xs-6">
                <label for="countrySelect">Выбор страны</label>
                <select size="9" multiple class="form-control" name="countries[]" id="countrySelect disabledInput">
                  <?php                  
                    foreach ( $countries as $c ) {
                      echo "<option";                      
                      if ( ! $new_task ) {
                        foreach ( $task['countries'] as $tc ) {
                          if ( $tc['id'] == $c['id'] ) {
                            echo " selected";
                            break;
                          }
                        }
                      } else {
                        echo " selected";
                      }
                      
                      echo " value=". $c['id'] .">" . $c['name'] ."</option>";
                    }                  
                  ;?>                  
                </select>
              </fieldset>              
              <fieldset class="form-group col-xs-6">
                <label for="userSelect">Выбор пользователей для просмотра</label>
                <select size="9" multiple class="form-control" name="users[]" id="userSelect">
                  <?php                  
                    foreach ( $users as $other_user ) {
                      echo "<option";                      
                      if ( ! $new_task ) {
                        foreach ( $task['users'] as $task_user ) {
                          if ( $task_user['user_id'] == $other_user['id'] ) {
                            echo " selected";
                            break;
                          }
                        }
                      } else {
                        echo " selected";
                      }
                      
                      echo " value=". $other_user['id'] .">" . $other_user['username'] ."</option>";
                    }                  
                  ;?>                  
                </select>
              </fieldset>
            </div>
            <div class="row">              
              <fieldset class="form-group col-xs-6">
                <label for="fileSelect">Выбор файлов</label>
                <select size="9" multiple class="form-control" name="files[]" id="fileSelect">
                  <?php                  
                    foreach ( $files as $other_file ) {
                      echo "<option";                                            
                      foreach ( $task['files'] as $task_file ) {
                        if ( $task_file['file_id'] == $other_file['file_id'] ) {
                          echo " selected";
                          break;
                        }
                      }                      
                      
                      echo " value=". $other_file['id'] .">" . $other_file['name'] ."</option>";
                    }                  
                  ;?>                  
                </select>
              </fieldset>
              <fieldset class="form-group col-xs-6">
                <label for="id2Select">Список версий приложений</label>
                <select size="9" multiple class="form-control" name="application_versions[]" id="id2Select">
                  <option value="0"<?php if( $new_task ) echo " selected"; ?>>Все</option>
                  <?php                  
                    foreach ( $application_versions as $application_version ) {
                      echo "<option";                      
                      if ( ! $new_task ) {
                        foreach ( $task['application_versions'] as $task_application_version ) {
                          if ( $task_application_version['application_version'] == $application_version['application_version'] ) {
                            echo " selected";
                            break;
                          }
                        }
                      }                                              
                      echo " value=". $application_version['application_version'] .">" . $application_version['application_version'] ."</option>";
                    }                  
                  ;?>
                </select>
              </fieldset>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="delete_after_finish" <?php if ( $task["delete_after_finish"] ) echo "checked"; ?>> Самоудаление после завершения
              </label>
            </div>
            <button type="submit" name="save" class="btn btn-primary">Сохранить</button>
            <?php if ( ! $new_task ): ?>
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
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
