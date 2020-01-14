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
//Change connection encoding to UTF-8
if (!mysqli_set_charset($connect, "utf8")) {
	    printf("Error loading character set utf8: %s\n", mysqli_error($connect));
	        exit();
} else {
	    printf("Current character set: %s\n", mysqli_character_set_name($connect));
}
?>
