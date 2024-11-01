<?php
/**
* Repair tool for users with no PHPmyAdmin...
*
* Easy to configure and use
* For all the people who upload WP from localhost to a remote server,
* change WP directory name or change domain.
* 
* Includes:
*  - tinyPMA for making backups of WP (the export exploit was done by myself)
*  - wp-umr for repair URI-Path of WordPress in the Database
*
* HowTo:
*  - upload whole directory in your WordPress directory
*  - Be sure the wp-config.php file is setup right
*  - run index.php in the wp_smr directory
*  - follow the instructions on screen
* 
* Requiments
*  - WordPress with right configured wp-config.php file
*
* @name       wp_umr (URI move repair)
* @category   tools
* @package    SnippetCase
* @author     Christian Krause <googol@snippetcase.com>
* @copyright  2005-2008 Christian Krause
* @license    GPL
* @version    0.7
* @link       www.snippetcase.com    
* @since      2008/May/02
* 
* @fix        0.7 [2008/May/15]
*               -> fix for connection charset
*               -> trouble with MySQL 5 is fixed
*               -> permission-bug in tinyPMA -> tinyPMA/libaries/Config.class.php
* 
*/

require_once 'wp-connector.php';

if (! @mysql_connect($cfg_MySQL_HOST, $cfg_MySQL_USER, $cfg_MySQL_PASSWORD) ) {
    $connectStateMsg .= '<p><strong>Connected with: </strong>' . $cfg_MySQL_HOST . ' 
                         <img src="images/failure.png" alt="failure" title="failure" /></p>';
} else {
    $connectStateMsg .= '<p><strong>Connected with: </strong>' . $cfg_MySQL_HOST . ' 
                         <img src="images/ok.png" alt="ok" title="ok" /></p>';
}

if ( ! @mysql_select_db($cfg_MySQL_DATABASE) ) {
    $connectStateMsg .= '<p><strong>Selected DB:</strong> ' . $cfg_MySQL_DATABASE . ' 
                         <img src="images/failure.png" alt="failure" title="failure" /></p>';
} else {
    $connectStateMsg .= '<p><strong>Selected DB:</strong> ' . $cfg_MySQL_DATABASE . ' 
                         <img src="images/ok.png" alt="ok" title="ok" /></p>';
}

$sqlState         = 'SELECT
                        `option_value`
                     FROM
                        `wp_options`
                     WHERE
                        `wp_options`.`option_id` = 1
                     AND
                        `wp_options`.`blog_id` = 0
                     AND
                     CONVERT( `wp_options`.`option_name` USING ' . @mysql_real_escape_string( $cfg_MySQL_CHARSET ) . ' ) = "siteurl" LIMIT 1';

$sqlRepairSiteurl = 'UPDATE
                        `' . @mysql_real_escape_string( $cfg_MySQL_DATABASE ) . '`.`wp_options` 
                     SET
                        `option_value` = "' . @mysql_real_escape_string( $_POST['newURI'] ) . '"
                     WHERE
                     CONVERT( `wp_options`.`option_name` USING ' . @mysql_real_escape_string( $cfg_MySQL_CHARSET ) . ' ) = "siteurl" LIMIT 1';

$sqlRepairHome    = 'UPDATE
                        `' . @mysql_real_escape_string( $cfg_MySQL_DATABASE ) . '`.`wp_options` 
                     SET
                        `option_value` = "' . @mysql_real_escape_string( $_POST['newURI'] ) . '"
                     WHERE
                     CONVERT( `wp_options`.`option_name` USING ' . @mysql_real_escape_string( $cfg_MySQL_CHARSET ) . ' ) = "home" LIMIT 1';

mysql_query ( 'SET NAMES "' . @mysql_real_escape_string( $cfg_MySQL_CHARSET ) . '"' );
mysql_query ( 'SET CHARACTER SET "' . @mysql_real_escape_string( $cfg_MySQL_CHARSET ) . '"' );

if ( !empty( $_POST['newURI'] ) && strlen( $_POST['newURI'] ) > 3 ) { //why 3? shortest domain got 3 character or higher .cc .to ...

    if ( !@mysql_query( $sqlRepairSiteurl ) || !@mysql_query( $sqlRepairHome ) ) {
        $connectStateMsg = '<p><strong>Repair wasn\'t successfull </strong> 
                             <img src="images/failure.png" alt="failure" title="failure" />
                            </p>';
    } else {
        $connectStateMsg = '<p><strong>Repair was successfull </strong>
                             <img src="images/ok.png" alt="ok" title="ok" /><br />
                            <strong>Important: </strong>Don\'t forget update "Permalinks"
                            </p>';
    }

}

$aSqlData = @mysql_fetch_array( mysql_query( $sqlState ) );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>wp-umr</title>
    
    <style type="text/css" title="currentStyle">
        @import "style.css";
    </style>
</head>
<body>

<div id="layout">
    <h1> wp-umr (URI move repair) </h1>
    <div id="contentContainer">
        <div id="contentBox">
            <div class="toolBox">
            <?php
            if ( $howTo == 1 ) :
            ?>
            <p>HowTo:</p>
                <ul>
                    <li>Upload whole directory in your <strong>WordPress directory</strong></li>
                    <li>Make sure that the <strong>wp-config.php</strong> file is setup right (wp-umr is need the config for get right informations of DB)</li>
                </ul>
            <?php
            endif;
            ?>
            <?php echo $connectStateMsg; ?>
            </div>
            <div class="toolBox">
            
            <strong>First make an Backup of your existing Database:</strong> <a href="tinyPMA">[ with tinyPMA ]</a>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

                <p class="margin-top">Old URI:</p>
                    <input type="text" name="oldURI" disabled="disabled" value="<?php echo $aSqlData['option_value']; ?>" />
                <p class="margin-top">New URI:</p>
                    <input type="text" name="newURI" />
                <p>ex. http://www.example.com</p>

                <button type="submit" name="repair" value="do">repair</button>

            </form>
            </div>
        </div>
    </div>
    <div id="footer">
        <img src="images/gplv3.gif" alt="released under GPL" title="released under GPL" />
        <img src="images/pma_logo.gif" alt="released under GPL" title="released under GPL" />
        | code by <a href="http://www.snippetcase.com">[ googol ]</a></div>
</div>

</body>
</html>
<?php

@mysql_close();

?>
