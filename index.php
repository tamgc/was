<?php include("./inc/header.inc"); ?>
  <form method="get" action="./query.php">
    <p class="first">Search:
    <input type="text" name="query" size="40" />
    <input type="submit" value="Search" />
    <a href="./advance.php">Advance Search</a>
    </p>
  </form>
  <hr />

  <p>Release: <br />
  <?php 
    include('./inc/connect.inc');
    $result = mysql_query("SELECT * FROM Releases ORDER BY Releases.Set");
    while($row = mysql_fetch_array($result))
    {
      echo('<a href="./query.php?release=' . $row['Release'] . 
           '">' . $row['Release'] . '</a> ');
    }
  ?>
  </p>
  <p>Faction:
  <?php 
    $result = mysql_query("SELECT DISTINCT Alliance FROM Factions");
    while($row = mysql_fetch_array($result))
    {
      echo('<a href="./query.php?alliance=' . $row['Alliance'] . 
           '">' . $row['Alliance'] . '</a> ');
    }
  ?>
  </p>
  <p>Nation:<br />
  <?php 
    $result = mysql_query("SELECT * FROM Factions");
    while($row = mysql_fetch_array($result))
    {
      echo('<a href="./query.php?faction=' . $row['Faction'] . 
           '"><img src="./images/faction/' . $row['Faction'] . 
	   '-lg.png" alt="' . $row['Faction'] . '" /></a> ');
    }
  ?>
  </p>
  <hr />
  <p>Please <a href="./contact.php">contact me</a> with any errors,
   or feedback.</p>
  <p>Also, if you're feeling generous, you can make a donation via PayPal. Any amount would be appreciated: 
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align:center">
  <input type="hidden" name="cmd" value="_s-xclick">
  <input type="hidden" name="hosted_button_id" value="3902227">
  <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
  <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
  </form>
  </p>
<?php include("./inc/footer.inc"); ?>
