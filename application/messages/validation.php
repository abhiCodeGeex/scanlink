<?php defined('SYSPATH') or die('No direct script access.');
array(
'username' => array(
   'not_empty' => 'This username is already...',
   'regex' => 'Username must not be empty.',
   'unique_username' => 'Password or username is incorrect.',
),
'password' => array(
   'not_empty' => ':field must be within the range of :param1 to :param2',
   'min_length' => 'The password and password confirmation ..',
),
);
?>