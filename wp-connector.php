<?php

/*
 * Do nothing here
 */

if ( is_file( realpath('../wp-config.php') ) ) {

    require_once '../wp-config.php';

    $cfg_MySQL_HOST     = DB_HOST;
    $cfg_MySQL_USER     = DB_USER;
    $cfg_MySQL_PASSWORD = DB_PASSWORD;
    $cfg_MySQL_DATABASE = DB_NAME;
    $cfg_MySQL_CHARSET  = DB_CHARSET;
    
    $connectStateMsg .= '<p><strong>Located "wp-config.php" file</strong> <img src="images/ok.png" alt="ok" title="ok" /></p>';

} else {

    $connectStateMsg .= '<p><strong>Can\'t find "wp-config.php" file</strong> <img src="images/failure.png" alt="failure" title="failure" /></p>';
    $howTo = 1;

}

?>
