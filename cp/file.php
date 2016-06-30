<?php require_once( __DIR__ . "/init.php"); ?>
<?php require_once( __DIR__ . "/controller/file.php"); ?>

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
    <link href="css/jasny-bootstrap.min.css" rel="stylesheet">
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
              <li class="active"><a href="files">Файлы</a><span class="sr-only">(current)</span></li>
              <li><a href="settings">Настройки</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">          
          <h2 class="sub-header">Файл: <?php echo $file["name"];?> </h2>
          <form method="post" enctype='multipart/form-data'>
            <fieldset class="form-group">              
              <label for="inputName">Название</label>              
              <input class="form-control" name="name" id="inputName" placeholder="Введите название" value="<?php echo $file["name"];?>">
            </fieldset>
            <fieldset class="form-group">
              <label for="inputSize">Размер</label>
              <div class="input-group">              
                <input type="text" id="inputSize" class="form-control" readonly placeholder="<?php if ( isset( $file['size'] ) ) echo $file["size"];?>" aria-describedby="basic-addon2">
                <span class="input-group-addon" id="basic-addon2">b</span>
              </div>
            </fieldset>
            <div class="panel panel-default">
              <div class="panel-heading">Загрузка файла                
              </div>
              <div class="panel-body">
                <button type="button" id="change-current-file" class="btn btn-warning <?php if ( ! $file ) echo 'hidden'; ?>">Заменить текущий файл</button>
                <div id="file-form-upload" <?php if ( $file ) echo 'class="hidden"' ;?>>
                  <ul class="nav nav-tabs">                  
                    <li class="active"><a data-toggle="tab" href="#menu1">С компьютера</a></li>
                    <li><a data-toggle="tab" href="#menu2">URL</a></li>
                  </ul>              
                  <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active">
                      <h3></h3>
                      <p>Загрузить файл с компьютера</p>
                      <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                        <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Выбрать файл</span><span class="fileinput-exists">Изменить</span><input type="file" name="file"></span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Удалить</a>
                      </div>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                      <h3></h3>
                      <p>Загрузка файла по ссылке</p>
                      <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">URL</span>
                        <input type="text" class="form-control" name="url" placeholder="URL" aria-describedby="basic-addon1">                        
                      </div>
                      <div class="checkbox">
                        <label>
                            <input type="checkbox" name="dynamic" checked > Динамическая ссылка
                        </label>
                      </div>
                    </div>                  
                  </div>                  
                </div>
              </div>
            </div>
            <?php if ( ! $file ): ?>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="xor" checked > Зашифровать файл
              </label>
            </div>
            <?php endif; ?>            
            <br/>
            <button type="submit" name="save" class="btn btn-primary">Сохранить</button>
            <?php if ( $file ): ?>
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
    <script src="js/jasny-bootstrap.min.js"></script>
    <script>
    $(document).ready( function(){            
      
      //$('.fileinput').fileinput();
      $("#change-current-file").on('click', function(){
        $("#file-form-upload").removeClass("hidden");
        $("#change-current-file").hide();
      });
      
      function changeXorStatus() {
        if ( $('input[name="dynamic"]').is(":checked") ) {          
          $( 'input[name="xor"]' ).attr("disabled", "disabled");
          $( 'input[name="xor"]' ).attr("checked", null );
        } else {
          $( 'input[name="xor"]' ).attr("checked", null );
          $( 'input[name="xor"]' ).attr("disabled", null);
        }
      }
      
      // on select URL tab
      $('input[name="dynamic"]').change( changeXorStatus );
      
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        
        var target = $(e.target).attr("href") // activated tab
        
        if ( target != "#menu2" )
          $('input[name="dynamic"]').attr("checked", null);
          
        changeXorStatus();
      });

    });
    
    </script>
  </body>
</html>
