<?php
$host     = 'localhost';
$db_name  = 'timehub_db';
$username = 'root';
$password = '';  

 $conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>