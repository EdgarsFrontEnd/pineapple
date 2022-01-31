<?php

// adjust accordingly
$host = 'edgars-pujats.magebithr.com';
$username = 'root';
$password = 'test12345';
$database = 'pineapple';

$connection = mysqli_connect($host, $username, $password, $database);
if(!$connection){
    echo 'connection error: ' . mysqli_connect_error();
}

?>