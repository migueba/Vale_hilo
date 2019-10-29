<?php
  // Database Structure
  $host="192.168.1.13";
  $username="root";
  $password="";
  $databasename="urdido_engomado";
  $puertodb="3306";

  $mysqli = new mysqli($host,$username,$password,$databasename,$puertodb);

  $mysqli->set_charset("utf8");

?>
