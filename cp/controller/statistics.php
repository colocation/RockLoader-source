<?php    
    if ( ! isAdmin() ){        
        redirect( 'Location: /cp/tasks' );
    }
    
    $total_users = $ClientModel->getClientCount();
    $online_users = $ClientModel->getOnlineClientCount();
    $new_users = $ClientModel->getNewClientCount();
    
    if( count( $total_users ) > 0 )
        $total_users = $total_users[0]['count'];
    else
        $total_users = 0;
    
    if( count( $online_users ) > 0 )
        $online_users = $online_users[0]['count'];
    else
        $online_users = 0;
        
    if( count( $new_users ) > 0 )
        $new_users = $new_users[0]['count'];
    else
        $new_users = 0;    
        
    $total_daily = $ClientModel->getTotalDaily();
    $daily_statistic = $ClientModel->getDailyStatistic();
    $daily_online = $ClientModel->getDailyOnline();
    $week_online = $ClientModel->getWeekOnline();
    $month_online = $ClientModel->getMonthOnline();
    
    // Start date
    $date = '2009-12-06';
    // End date
    $end_date = '2020-12-31';
    
    $tbody = '';
    $statistic_array = array();
    
    $week = array(
        "total" => 0,
        "online" => 0,
        "os" => array(),
        "country" => array(),
    );
    
    for ( $i = 0; $i < 7; $i++ ) {
        $date = date ( "Y-m-d", strtotime( "-" . $i . "day", strtotime( date( "Y-m-d" ) ) ) );
        
        $tbody .='<tr statistics_id="'. $i .'">' . "<td>". $date ."</td>";        
                
        //Total
        $tbody .= "<td>" . $total_users . "</td>";
        
        // New        
        $today = 0;
        foreach( $total_daily as $total ){
            if( $total['date'] == $date ){
                $today += $total['count'];
                $week["total"] += $total['count'];                
            }
        }
        $tbody .= "<td>". $today ."</td>";;
        
        // Online
        $do = "<td>0</td>";
        foreach( $daily_online as $online ){
            if( $online['date'] == $date ){
                $do ="<td>". $online['count'] ."</td>";                
                break;
            }
        }
        $tbody .= $do;        
        
        //OS, Country
        
        $date_os_array = array();
        $date_country_array = array();
        $date_online = 0;
                
        foreach ( $daily_statistic as $statistics ) {
                                                       
            if ( $statistics['date'] == $date ) {
                                                            
                if ( !isset( $date_os_array[ $statistics['os'] ] ) )
                    $date_os_array[ $statistics['os'] ] = 1;
                else 
                    $date_os_array[ $statistics['os'] ] ++;                    
                
                if ( !isset( $week["os"][ $statistics['os'] ] ) )
                    $week["os"][ $statistics['os'] ] = 0;
                $week["os"][ $statistics['os'] ] ++;
                    
                if ( !isset( $date_country_array[ $statistics['country_id'] ] ) )
                    $date_country_array[ $statistics['country_id'] ] = 1;
                else
                    $date_country_array[ $statistics['country_id'] ] ++;
                
                if ( !isset( $week["country"][ $statistics['country_id'] ] ) )
                    $week["country"][ $statistics['country_id'] ] = 0;
                $week["country"][ $statistics['country_id'] ] ++;                                                                
            }                            
        }
        
        $tbody .= "<td>" . count( $date_country_array ) . "</td>";                            
        $tbody .= "<td>" . count( $date_os_array ) . "</td>";        
                
        $statistic_array[ $i ] = array(
            "os" => $date_os_array,
            "country" => $date_country_array
        );
        
        //
        $tbody .="</tr>";
    }
    
    // Week
    $tbody .='<tr class="bold" statistics_id="7"><td>За неделю</td><td>' . $total_users . '</td><td>'. $week['total'] ."</td><td>". $week_online[0]['count']
        ."</td><td>". count( $week['country'] ) ."</td><td>". count( $week["os"] )  ."</td></tr>";
    $statistic_array[ $i++ ] = array(
        "os" => $week['os'],
        "country" => $week['country']
    );
 
    //Month
    $month = array(
        "total" => 0,
        "online" => 0,
        "os" => array(),
        "country" => array(),
    );
    
    foreach( $total_daily as $td ){
        $month["total"] += $td["count"];
    }        
                
    foreach( $daily_statistic as $statistics ){
                    
        if( ! isset( $month["os"][ $statistics["os"] ] ) )
            $month["os"][ $statistics["os"] ] = 0;
        $month["os"][ $statistics["os"] ] ++;
                
        if( ! isset( $month["country"][ $statistics["country_id"] ] ) )
            $month["country"][ $statistics["country_id"] ] = 0;
        $month["country"][ $statistics["country_id"] ] ++;
        
    }
         
    $tbody .='<tr class="u-selected bold" statistics_id="8"><td>За месяц</td><td>' . $total_users . '</td><td>'. $month["total"] ."</td><td>". $month_online[0]['count']
        ."</td><td>". count( $month['country'] ) ."</td><td>". count( $month["os"] )  ."</td></tr>";
    

    $statistic_array[$i++] = array(
        "os" => $month['os'],
        "country" => $month['country']
    );
    
?>