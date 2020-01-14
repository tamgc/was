<?php
include('./inc/connect.inc');

$field = "Name LIKE '%" . addslashes($_GET['query']) . "%' OR UnitID IN (SELECT UnitID FROM Unit_Abilities JOIN Abilities ON Unit_Abilities.AbilityName=Abilities.AbilityName WHERE AbilityText LIKE '%" 
. addslashes($_GET['query']) . "%' OR Abilities.AbilityName LIKE '%"
. addslashes($_GET['query']) . "%') OR Notes";

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

//Parse the query
if($_GET['release'])
{
  $query = " = '" . addslashes($_GET['release']) . "'";
  $field = "Units.Release";
} else if($_GET['alliance']){
  $query = " IN (SELECT Faction FROM Factions where Alliance = '" .
           addslashes($_GET['alliance']) . "')";
  $field = "Faction";
} else if($_GET['faction']){
  $query = " = '" . addslashes($_GET['faction']) . "'";
  $field = "Faction";
} else {
  $query = " LIKE '%" . addslashes($_GET['query']) . "%'";
}
$result = mysqli_query($connect,"SELECT UnitID, Name, Points, Faction, Type, Subtype, 
Year FROM Units WHERE " . $field . $query . " ORDER BY " . $sort);

$numrows = mysqli_num_rows($result);

if(!$result)
{
  echo("<p>Error performing query.</p>");
  exit();
}

include("./inc/header.inc");
echo('<p class="first">' . $numrows . ' record(s) found.</p>');

//Grab current query so it could be sorted
$curQuery = $_SERVER['REQUEST_URI'];
//Chop off any previous sorts if any are found
if(strripos($curQuery, "&sort="))
{
  $curQuery = substr_replace($curQuery, '', strripos($curQuery, "&sort="));
}

//Parsing the Type/Subtype of the unit, and setting the display var
echo('<table><tr><th><a class="th" href=".' . $curQuery . 
     '&sort=Name">Name</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '&sort=Points">Points</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '&sort=Faction">Nation</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '&sort=Year">Year</a></th>' .
     '<th><a class="th" href=".' . $curQuery . 
     '&sort=Type">Type</a></th></tr>');

    $i = 0;
    //Print results
    while($row = mysqli_fetch_array($result))
    {
      if($i % 2)
      {
        echo('<tr><td class="alt1"><a href="./unit.php?ID=' . $row['UnitID'] . 
	     '">' . $row['Name'] . '</a></td>
	     <td class="alt1">' . $row['Points'] . ' pts.</td>
	     <td class="alt1">' . $row['Faction'] . 
	     '</td><td class="alt1">' . $row['Year'] . 
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
	     '</td><td class="alt">' . $row['Year'] . 
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
?>
