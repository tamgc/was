<?php
include('../local/connect.inc');

//Connect to the DB server
$connect = mysqli_connect($dbs, $username, $password, $db);
if(!$connect){
  echo "Error: Unable to connect to MySQL." . PHP_EOL;
  echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
  echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
  exit;
}
?>
