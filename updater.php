<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// define('SYSPATH', '/var/www/scanlink.com.au/');

require('application/bootstrap.php');
$client_id = DB::select('*')->from('users')->as_array();