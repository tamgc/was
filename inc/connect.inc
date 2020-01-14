<?php
include('../local/connect.inc');

//Connect to the DB server
$connect = @mysql_connect($dbs, $username, $password);
if(!$connect)
{
  echo( "<p>Unable to connect to the database server at this time.</p>" );
  exit();
}

//Connect to the DB
mysql_select_db($db);
if(! @mysql_select_db($db) )
{
  echo( "<p>Unable to locate the database at this time.</p>" );
  exit();
}
?>
