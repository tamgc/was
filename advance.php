<?php
session_start();
include("./inc/header.inc");
include('./inc/connect.inc');

$sort = "Name";

if($_GET['sort'])
{
  if($_GET['sort'] == 'Type')
  {
    $sort = 'Type,Subtype';
  } else {
    $sort = addslashes($_GET['sort']);
  }
}

if(isset($_POST['search']))
{
  $_SESSION = $_POST;
  if($_POST['username'] != '')
  {
    header( 'Location: http://www.tamgc.net' );
  }
  //Build the query parameters
  if($_POST['name'] != '')
  {
    $query = $query . " AND (Name LIKE '%" . addslashes($_POST['name']) . 
             "%' OR Notes LIKE '%" . addslashes($_POST['name']) . "%' OR UnitID IN (SELECT UnitID FROM Unit_Abilities JOIN Abilities ON Unit_Abilities.AbilityName=Abilities.AbilityName WHERE AbilityText LIKE '%" . 
	     addslashes($_POST['name']) . "%' OR Abilities.AbilityName LIKE '%"
	     . addslashes($_POST['name']) . "%'))";
  }
  if($_POST['nation'] != '')
  {
    $query = $query . " AND Faction = '" . $_POST['nation'] . "'";
  }
  if($_POST['points'] != '')
  {
    $query = $query . " AND Points " . $_POST['pointsEval'] .  
    addslashes($_POST['points']);
  }
  if($_POST['type'] != '')
  {
    $query = $query . " AND (Type = '" . addslashes($_POST['type']) . 
             "' OR Subtype = '" . addslashes($_POST['type']) . "')";
  }
  if($_POST['year'] != '')
  {
    $query = $query . " AND Year " . $_POST['yearEval'] .  
    addslashes($_POST['year']);
  }
  if($_POST['speed'] != '')
  {
    $query = $query . " AND Speed " . $_POST['speedEval'] .  
    addslashes($_POST['speed']);
  }
  if($_POST['flagship'] != '')
  {
    $query = $query . " AND Flagship " . $_POST['flagshipEval'] .  
    addslashes($_POST['flagship']);
  }
  if($_POST['carrier'] != '')
  {
    $query = $query . " AND Carrier " . $_POST['carrierEval'] .  
    addslashes($_POST['carrier']);
  }
  //Checks for anything submited via range or attack
  if($_POST['range0'] != '' || $_POST['range1'] != '' 
     || $_POST['range2'] != '' || $_POST['range3'] != ''
     || $_POST['attack'] != '')
  {
    if($_POST['range0'] != '')
    {
      $range = $range . " AND Range0 " . $_POST['range0Eval'] .
               addslashes($_POST['range0']);
    }
    if($_POST['range1'] != '')
    {
      $range = $range . " AND Range1 " . $_POST['range1Eval'] .
               addslashes($_POST['range1']);
    }
    if($_POST['range2'] != '')
    {
      $range = $range . " AND Range2 " . $_POST['range2Eval'] .
               addslashes($_POST['range2']);
    }
    if($_POST['range3'] != '')
    {
      $range = $range . " AND Range3 " . $_POST['range3Eval'] .
               addslashes($_POST['range3']);
    }
    /*Queries for anything with:
      any range given
      or anything with attack name
      or if both is given, attack name with given ranges
    */
    $query = $query . " AND UnitID IN 
             (SELECT UnitID FROM Unit_Attack WHERE Attack LIKE '%" .
             $_POST['attack'] . "%'" . $range . ")";
  }
  if($_POST['armor'] != '')
  {
    $query = $query . " AND Armor " . $_POST['armorEval'] .  
    addslashes($_POST['armor']);
  }
  if($_POST['vitalarmor'] != '')
  {
    $query = $query . " AND VitalArmor " . $_POST['vitalarmorEval'] .  
    addslashes($_POST['vitalarmor']);
  }
  if($_POST['hullpoints'] != '')
  {
    $query = $query . " AND HullPoints " . $_POST['hullpointsEval'] .  
    addslashes($_POST['hullpoints']);
  }
  if($_POST['ability'][0] != '')
  {
    foreach($_POST['ability'] as &$temp)
    {
      $query = $query . " AND UnitID IN 
               (SELECT UnitID FROM Unit_Abilities WHERE AbilityName = '" .
               $temp . "')";
    }
    unset($temp);
  }
  if($_POST['faction'] != '')
  {
    $query = $query . "AND Faction IN 
             (SELECT Faction FROM Factions WHERE Alliance = '" .
             $_POST['faction'] . "')";
  }
  if($_POST['release'] != '')
  {
    $query = $query . " AND Units.Release = '" . $_POST['release'] . "'";
  }
  if($_POST['rarity'] != '')
  {
    $query = $query . " AND Rarity = '" . $_POST['rarity'] . "'";
  }
  if(!isset($_SESSION['query']))
  {
    $_SESSION['query'] = $query;
  }
}

if(isset($_POST['search']) || $_GET['sort'])
{
  //"WHERE 1" so query would work with "AND"s on any addition
  $result = mysql_query("SELECT * FROM Units WHERE 1" . $_SESSION['query'] . 
            " ORDER BY " . $sort);

  if(!$result)
  {
    echo("<p>Error performing query.</p>");
    exit();
  }

$numrows = mysql_num_rows($result);
echo('<p class="first">' . $numrows . ' record(s) found.</p>');

//Grab current query so it could be sorted
$curQuery = $_SERVER['REQUEST_URI'];
//Chop off any previous sorts if any are found
if(strripos($curQuery, "?sort="))
{
  $curQuery = substr_replace($curQuery, '', strripos($curQuery, "?sort="));
}

//Parsing the Type/Subtype of the unit, and setting the display var
echo('<table><tr><th><a class="th" href=".' . $curQuery . 
     '?sort=Name">Name</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '?sort=Points">Points</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '?sort=Faction">Nation</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '?sort=Type">Type</a></th></tr>');

    $i = 0;
    //Print results
    while($row = mysql_fetch_array($result))
    {
      if($i % 2)
      {
        echo('<tr><td class="alt1"><a href="./unit.php?ID=' . $row['UnitID'] . 
	     '">' . $row['Name'] . '</a></td>
	     <td class="alt1">' . $row['Points'] . ' pts.</td>
	     <td class="alt1">' . $row['Faction'] . 
	     '</td><td class="alt1">' . $row['Type']);
	if($row['Subtype'])
	{
	  echo(' - ' . $row['Subtype']);
	}  
	echo('</td></tr>');
      } else {
        echo('<tr><td class="alt"><a href="./unit.php?ID=' . $row['UnitID'] . 
	     '">' . $row['Name'] . '</a></td>
	     <td class="alt">' . $row['Points'] .  ' pts.</td>
	     <td class="alt">' . $row['Faction'] . 
	     '</td><td class="alt">' . $row['Type']);
	if($row['Subtype'])
	{
	  echo(' - ' . $row['Subtype']);
	}  
	echo('</td></tr>');
      }
      $i++;
    }
    echo('</table>');
    include("./inc/footer.inc");
  exit();
}
?>

<?php 
//Clear previous session data
session_destroy();
?>
<table>
<form method="post" action="./advance.php">
<div id="form0">
  <input type="text" name="username" size="30"><br />
</div>

<tr>
<td>Search:</td><td />
<td><input type="text" name="name" size="40" /></td>
</tr>

<tr><td>Nation: </td><td />
<td><select name="nation">
<option value="">Any</option>
<?php 
  $result = mysql_query("SELECT * FROM Factions");
  while($row = mysql_fetch_array($result))
  {
    echo('<option value="' . $row['Faction'] . 
         '">' . $row['Faction'] . '</option>');
  }
?>
</select></td>
</tr>
<tr><td>Points:</td>
<td><select name="pointsEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="points" size="2" /></td>
</tr>
<tr><td>Type:</td><td />
<td><select name="type">
<option value="">Any</option>
<?php 
  $result = mysql_query("SELECT DISTINCT Type FROM Units ORDER BY Type");
  while($row = mysql_fetch_array($result))
  {
    echo('<optgroup label="' . $row['Type'] . '"><option value="' . 
         $row['Type'] . '">Any ' . $row['Type'] . '</option>');
    $subresult = mysql_query('SELECT DISTINCT Subtype FROM Units WHERE Type = "'
                             . $row['Type'] . '" ORDER BY Subtype');
    while($subrow = mysql_fetch_array($subresult))
    {
      if($subrow['Subtype'])
      {
        echo('<option value="' . $subrow['Subtype'] . 
             '">' . $subrow['Subtype'] . '</option>');
      }
    }
  }
?>
</select></td>
</tr>
<tr><td>Year:</td>
<td><select name="yearEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="year" size="4" /></td>
</tr>
<tr><td>Speed:</td>
<td><select name="speedEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="speed" size="1" /></td>
</tr>
<tr><td>Flagship:</td>
<td><select name="flagshipEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="flagship" size="1" /></td>
</tr>
<tr><td>Carrier:</td>
<td><select name="carrierEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="carrier" size="1" /></td>
</tr>
<tr><td>Attack:</td><td />
<td><select name="attack">
<option value="">Any</option>
<?php 
  $result = mysql_query("SELECT AttackName FROM Attacks ORDER BY Priority");
  while($row = mysql_fetch_array($result))
  {
    switch($row['AttackName'])
    {
      case "Gunnery1":
        $attackName = "Main Gunnery";
	break;
      case "Gunnery2":
        $attackName = "Secondary Gunnery";
	break;
      case "Gunnery3":
        $attackName = "Tertiary Gunnery";
	break;
      default:
        $attackName = $row['AttackName'];
	break;
    }
    echo('<option value="' . $row['AttackName'] . 
         '">' . $attackName . '</option> ');
  }
?>
</select></td>
</tr>
<tr><td>Range 0:</td>
<td><select name="range0Eval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="range0" size="1" /></td></tr>
<tr><td>Range 1:</td>
<td><select name="range1Eval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="range1" size="1" /></td></tr>
<tr><td>Range 2:</td>
<td><select name="range2Eval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="range2" size="1" /></td></tr>
<tr><td>Range 3:</td>
<td><select name="range3Eval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="range3" size="1" /></td></tr>
<tr><td>Armor:</td>
<td><select name="armorEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="armor" size="1" /></td>
</tr>
<tr><td>Vital Armor:</td>
<td><select name="vitalarmorEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="vitalarmor" size="1" /></td>
</tr>
<tr><td>Hull Points:</td>
<td><select name="hullpointsEval">
<option value=">">></option>
<option value="=">=</option>
<option value="<"><</option>
</select></td>
<td><input type="text" name="hullpoints" size="1" /></td>
</tr>
<tr><td>Ability:</td><td />
<td><select name="ability[]" multiple size="5">
<option selected="selected" value="">Any</option>
<?php 
  $result = mysql_query("SELECT DISTINCT AbilityName FROM Abilities");
  while($row = mysql_fetch_array($result))
  {
    echo('<option value="' . $row['AbilityName'] . 
         '">' . $row['AbilityName'] . '</option> ');
  }
?>
</select>
<br />
<div id="note">
(Note: You can select multiple abilities by holding down Ctrl while selecting.)
</div>
</td>
</tr>

<tr><td>Faction:</td><td />
<td><select name="faction">
<option value="">Any</option>
<?php 
  $result = mysql_query("SELECT DISTINCT Alliance FROM Factions");
  while($row = mysql_fetch_array($result))
  {
    echo('<option value="' . $row['Alliance'] . 
         '">' . $row['Alliance'] . '</option> ');
  }
?>
</select></td>
</tr>
<tr><td>Release:</td><td />
<td><select name="release">
<option value="">Any</option>
<?php 
  $result = mysql_query("SELECT * FROM Releases ORDER BY Releases.Set");
  while($row = mysql_fetch_array($result))
  {
    echo('<option value="' . $row['Release'] . 
         '">' . $row['Release'] . '</option> ');
  }
?>
</select></td>
</tr>
<tr><td>Rarity:</td><td />
<td><select name="rarity">
<option value="">Any</option>
<?php 
  $result = mysql_query("SELECT DISTINCT Rarity FROM Units");
  while($row = mysql_fetch_array($result))
  {
    echo('<option value="' . $row['Rarity'] . 
         '">' . $row['Rarity'] . '</option> ');
  }
?>
</select></td>
</tr>
<tr><td colspan="3" class="center">
<input type="submit" name="search" value="Search" />
<input type="reset" value="Reset" /></td></tr>
</form>
</table>

<?php include("./inc/footer.inc"); ?>
