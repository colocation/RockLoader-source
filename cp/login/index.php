<?php require_once( __DIR__ . "/auth.php"); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <!-- Bootstrap -->
    <link href="<?php echo STATIC_PATH; ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo STATIC_PATH; ?>/css/login.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    
    <div class="container">

      <form class="form-signin" id="login" action="/cp/login/" method="post">
        <h2 class="form-signin-heading">Авторизуйтесь</h2>
        <label for="inputLogin" class="sr-only">логин</label>
        <input type="login" id="inputLogin" name="username" class="form-control" placeholder="Логин" required autofocus>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required>
        <div class="checkbox">
          <label>
            <!--input type="checkbox" value="remember-me"> Запомнить меня-->
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Вход</button>
      </form>

    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo STATIC_PATH; ?>/js/bootstrap.min.js"></script>
  </body>
</html>
