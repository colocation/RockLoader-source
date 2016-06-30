<?php
    require_once( __DIR__ . "/settings.php" );
    require_once( __DIR__ . "/core/functions.php" );    
    require_once( __DIR__ . "/core/models.php" );
    
    define('FILE_PATH', APPDATER_PATH . 'files/');
    define('GEOIP_DAT', APPDATER_PATH . 'core/GeoIP.dat');
    
    // Set Settings
    $PING_TIME = 5;
    $COMMAND = 'UPDATE';
    $DISABLE_GRAPHICS = 0;
    $settings = $SettingsModel->getSettings();
    
    if( isset( $settings[0]['ping'] ) )
        $PING_TIME = $settings[0]['ping'];
    
        
    if( isset( $settings[0]['command_to_del'] ) )
        if( $settings[0]['command_to_del'] == 1 )
            $COMMAND = 'DEL';
     
    if( isset( $settings[0]['disable_graphics'] ) )
        $DISABLE_GRAPHICS = $settings[0]['disable_graphics'];
?>

