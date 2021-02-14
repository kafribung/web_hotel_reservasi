<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_localhost = "localhost";
$username_localhost = "root";
$password_localhost = "";
$database_localhost = "php_hotel_reverse";
$localhost = mysqli_connect($hostname_localhost, $username_localhost, $password_localhost, $database_localhost ) or trigger_error(mysql_error(),E_USER_ERROR); 
?>