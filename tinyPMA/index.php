<?php

require_once realpath('../../wp-config.php');

header('Location: ' . realpath($_SERVER['PHP_SELF']) . 'db_export.php?db=' . DB_NAME);
exit();

?>
