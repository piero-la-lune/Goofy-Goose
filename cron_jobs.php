<?php

define('D_HOUR', 3600);
define('D_DAY', 86400);
define('D_WEEK', 604800);
define('D_MONTH', 2419200);



### CONFIG ###



### END CONFIG ###



$remote_addr = @$_SERVER['REMOTE_ADDR'];
if (PHP_SAPI != 'cli'
	&& (strncmp(PHP_SAPI, 'cgi', 3) || !empty($remote_addr))
) {
	# executed from a browser
	die('Can\'t be executed from a browser...');
}



$cron_job = true;
require 'index.php';

?>