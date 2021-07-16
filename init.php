<?php

require_once('config.php');
require_once('helpers.php');
require_once('functions.php');

$con = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($con, "utf8");
