<?php require_once( __DIR__ . "/init.php"); ?>
<?php require_once( __DIR__ . "/controller/statistics.php"); ?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Статистика</title>

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
      <div class="container" id="index">
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
            <li class="active"><a href="/cp/">Статистика <span class="sr-only">(current)</span></a></li>
            <?php if ( isAdmin() ): ?>
              <li><a href="tasks">Задачи</a></li>
              <li><a href="users">Пользователи</a></li>
              <li><a href="files">Файлы</a></li>
              <li><a href="settings">Настройки</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">          
          <h2 class="sub-header">Статистика</h2>                  
          <div class="row" style="text-align: center; background: #f5f5f5;">            
            <div class="col-xs-6">              
              <h4>Онлайн: <?php echo $online_users; ?> из <?php echo $total_users; ?> </h4>
              <?php if( ! $DISABLE_GRAPHICS ) : ?>
              <ul class="pie">
                <li class="visualize" data-value="<?php echo $online_users; ?>">Онлайн (<?php echo $online_users; ?>)</li>
                <li class="visualize" data-value="<?php echo $total_users; ?>">Всего пользователей (<?php echo $total_users; ?>)</li>                
              </ul>
              <?php endif; ?>
            </div>
            <div class="col-xs-6">              
              <h4>Новые (за сутки): <?php echo $new_users; ?> </h4>
              <?php if( ! $DISABLE_GRAPHICS ) : ?>
              <ul class="pie">
                <li class="visualize" data-value="<?php echo $new_users; ?>">Новых (<?php echo $new_users; ?>)</li>
                <li class="visualize" data-value="<?php echo $total_users; ?>">Всего пользователей (<?php echo $total_users; ?>)<br></li>                
              </ul>
              <?php endif; ?>
            </div>            
          </div>
          <br>
          <div class="row" style="text-align: center;">               
            <div class="table-responsive">
              <table class="table table-striped statistic-table">
                <thead>
                  <tr>
                    <th>Дата</th>
                    <th>Всего</th>
                    <th>Новые</th>
                    <th>Онлайн</th>
                    <th>Страны</th>
                    <th>ОС</th>                
                  </tr>
                </thead>
                <tbody>
                  <?php echo $tbody; ?>               
                </tbody>
              </table>
            </div>
            <?php if( ! $DISABLE_GRAPHICS ) : ?>              
            <div class="row">
              <div class="col-md-4">
                <h5>Онлайн</h5>
                <ul class="pie2" id="pie-online">                            
                </ul>
                <h5>Новые</h5>
                <ul class="pie2" id="pie-new">                            
                </ul>                
              </div>
              <div class="col-md-4">
                <h5>Топ-20 стран</h5>
                <canvas id="pie-country" width="270" height="265"></canvas>  
              </div>
              <div class="col-md-4">
                <h5>Операционные системы</h5>
                <canvas id="pie-os" width="270" height="265"></canvas>  
              </div>                            
            </div>  
            <?php endif; ?>
            <div class="row">
              <h5>Статистика по странам</h5>
              <div class="table-responsive">
                <table class="table table-striped country-table">
                  <thead>
                    <tr>
                      <th>Страна</th>
                      <th>Количество</th>                      
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/visualize.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/Chart.HorizontalBar.js"></script>
    <script>
    $(document).ready(function() {
      
      <?php if( ! $DISABLE_GRAPHICS ) : ?>
      
      var
        statistics = <?php echo json_encode( $statistic_array ); ?>,
        u_all = parseInt( <?php echo $total_users; ?> )
      ;
      
      $('.pie').visualize({
        width: 100,
        height: 100,
        type: 'pie', // pie or chart
        legend: true
      });
      
      $(".statistic-table tbody tr").on('click', function() {
        $(".statistic-table tbody tr").removeClass("u-selected");
        $(this).addClass("u-selected");
        enableCharts();
      });
      
      enableCharts();
      
      function enableCharts (){      
        // Настраиваем чарты
              
        var u_new = parseInt( $(".u-selected").find("td:nth-child(3)").text() ),            
            u_online = $(".u-selected").find("td:nth-child(4)").text()
        ;
                
        $("#pie-new li").remove();
        $('<li class="visualize" data-value="'+ (u_new) +'">Новые ('+ (u_new) +')</li>').appendTo("#pie-new");
        $('<li class="visualize" data-value="'+ (u_all - u_new) +'">Всего (' + (u_all) + ')</li>').appendTo("#pie-new");        
                
        $("#pie-online li").remove();
        $('<li class="visualize" data-value="'+ (u_online) +'">Онлайн ('+ (u_online) +')</li>').appendTo("#pie-online");
        $('<li class="visualize" data-value="'+ (u_all - u_online) +'">Всего (' + (u_all) + ')</li>').appendTo("#pie-online");
                                  
        var row_index = $(".u-selected").index();  
        var statistic = statistics[ row_index ];
        
        $("#pie-country li").remove();
        $("#pie-os li").remove();
        
        console.log( statistic );
        
        var country_lables = [];
        var country_data = [];
        
        function bySortedValue(obj) {
          var tuples = [];
  
          for (var key in obj) tuples.push([key, obj[key]]);
  
          tuples.sort(function(a, b) { return a[1] < b[1] ? 1 : a[1] > b[1] ? -1 : 0 });
          
          return tuples;
        }

        var countries = bySortedValue( statistic['country'] );
        var n = 20;
        $(".country-table tbody").html('');
        
        for ( var k in countries ) {
          
          $('<tr><td>' + countries[k][0] +'</td><td>'+ countries[k][1] +'</td></tr>').appendTo(".country-table");
          
          if ( n >= 0 ) {          
            country_data.push( countries[k][1] );
            country_lables.push( countries[k][0] );
          }
          
          n--;
        }
                
        var os_lables = [];
        var os_data = [];
                
        for ( var key in statistic['os'] ) {        
          if ( statistic['os'].hasOwnProperty( key ) ) {
            os_data.push( statistic['os'][ key ] );
            os_lables.push( key );
          }
        }
                        
        $('.pie2').visualize({
          width: 110,
          height: 110,
          type: 'pie', // pie or chart
          legend: true
        });
        
        new Chart(
            document.getElementById("pie-os").getContext("2d")
          ).HorizontalBar( {
              labels: os_lables,
              datasets: [
                  {
                      fillColor: "green",
                      strokeColor: "green",
                      highlightFill: "green",
                      highlightStroke: "green",
                      data: os_data
                  }       
              ]
            }, {
              animation : false,
              showTooltips : false
            });
        
        new Chart(
            document.getElementById("pie-country").getContext("2d")
          ).HorizontalBar( {
              labels: country_lables,
              datasets: [
                  {
                      fillColor: "blue",
                      strokeColor: "blue",
                      highlightFill: "blue",
                      highlightStroke: "blue",
                      data: country_data
                  }     
              ]
            }, {
              animation : false,
              showTooltips : false
            });
        
      }
      
      <?php endif; ?>
    });
</script>

  </body>
</html>
