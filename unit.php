<?php
  include('./inc/connect.inc');
  include('./inc/header.inc');

  $UnitID = addslashes($_GET['ID']);
  //Query for general unit information
  $result = mysqli_query($connect,"SELECT * FROM Units INNER JOIN Releases 
            ON Units.Release = Releases.Release 
            WHERE UnitID = " . $UnitID);
  //Query for unit's attack information
  $attackQuery = mysqli_query($connect,"SELECT * FROM Unit_Attack,Attacks
                 WHERE UnitID = " . $UnitID .
                 " AND Attack = AttackName ORDER BY Priority");
  //Query for unit's abilities
  $abilityQuery = mysqli_query($connect,'SELECT * FROM Abilities
                  WHERE AbilityName IN
                  (SELECT AbilityName FROM Unit_Abilities
                  WHERE UnitID = ' . $UnitID . ')');
  //Query for unit's faction to determine background color
  $factionQuery = mysqli_query($connect,'SELECT Alliance FROM Factions
                  WHERE Faction = (SELECT Faction FROM Units
		  WHERE UnitID = ' . $UnitID . ')');
  $faction = mysqli_fetch_array($factionQuery);

  $row = mysqli_fetch_array($result);
  //General unit stats
  echo('<table width="450px"><tr><td colspan="5" class="img">
        <img src="./images/');
  //Check if image exists, if not, use default 'noimage.jpg'
  if(file_exists('./images/' . $row['Release'] . '/' . $row['SetNumber']
     . '.jpg'))
  {
    echo($row['Release'] . '/' . $row['SetNumber']);
  } else {
    echo('noimage');
  }
  echo('.jpg" /></td></tr>
        <tr><td colspan="4" class="inv">' . $row['Name'] . 
        '</td><td width="75px" rowspan="2" class="points ' .
        $faction['Alliance'] . '">' . $row['Points'] . 
        '</td></tr><tr><td rowspan="2" class="alt3" style="width:50px">
        <img src="./images/faction/' . 
        $row['Faction'] . '-sm.png" alt="' . $row['Faction'] . '" />' . 
        '</td><td colspan="3" class="small ' . $faction['Alliance'] . 
        '"><span class="left">' . $row['Type']);
        if($row['Subtype'])
        {
          echo(' - ' . $row['Subtype']);
	}
  echo('</span><span class="right">' . $row['Year'] . '</span>' .
       '</td></tr><tr><td class="small inv"> Speed - ');
  if($row['Speed'] != NULL)
  {
    echo($row['Speed']);
  } else if($row['Type'] = 'Aircraft') {
    echo('A');
  }
  echo('</td><td class="small inv" width="50px">');
  if($row['Flagship'])
  {
    echo('<img src="./images/symbols/Flagship.png" alt="Flagship" /> '
         . $row['Flagship']);
  }
  echo('</td><td class="small inv" width="50px">');
  if($row['Carrier'])
  {
    for($i = $row['Carrier']; $i > 0; $i--)
    {
      echo('<img src="./images/symbols/Carrier.png" alt="Carrier" />');
    }
  }
  echo('</td><td class="alt3" /></tr></table>');
  //Attack stats
  echo('<table width="450px"><tr>
        <td class="small inv center" width="75px">Attack</td>
        <td class="small inv center" width="50px">0</td>
        <td class="small inv center" width="50px">1</td>
        <td class="small inv center" width="50px">2</td>
        <td class="small inv center" width="50px">3</td>
        <td class="alt3" /></tr>');
  while($attack = mysqli_fetch_array($attackQuery))
  {
    //Check for Main Gunnery symbol, set the symbol name
    switch($attack['Attack'])
    {
      case "Gunnery1":
        $atkImg = $attack['Attack'] . '-' . $row['Type'];
        break;
      default:
        $atkImg = $attack['Attack'];
    }	
    echo('<tr><td class="inv center"><img src="./images/attacks/' . $atkImg .
         '.png" /></td><td class="atk">' . $attack['Range0'] .
         '</td><td class="atk">');
    if($attack['Range1'] != NULL)
    {
      echo($attack['Range1'] . '</td><td class="atk">');
    } else {
      echo('-' . '</td><td class="atk">');
    }
    if($attack['Range2'] != NULL)
    {
      echo($attack['Range2'] . '</td><td class="atk">');
    } else {
      echo('-' . '</td><td class="atk">');
    }
    if($attack['Range3'] != NULL)
    {
      echo($attack['Range3'] . '</td><td width="75px" class="alt3" /></tr>');
    } else {
      echo('-' . '</td><td class="alt3" /></tr>');
    }
  }
  //Armor stats
  echo('</table><table width="450px"><tr><td class="inv"> Armor </td>
       <td class="center">' . $row['Armor'] . 
       '</td><td class="inv"> Vital Armor </td>
       <td class="center">' . $row['VitalArmor'] . 
       '</td><td class="inv"> Hull Points </td>
        <td class="center">' . $row['HullPoints'] . 
       '</td></tr></table>');
  //Abilties
  echo('<table width="450px">');
  if($row['Flagship'] != NULL)
  {
    echo('<tr><td class="alt3 small">Flagship ' . $row['Flagship']);
  }
  if($row['Carrier'] != NULL)
  {
    echo('<tr><td class="alt3 small">Base ' . $row['Carrier'] . 
         ' Squadron(s)');
  }
  while($ability = mysqli_fetch_array($abilityQuery))
  {
    echo('<tr><td class="alt2 small">' . $ability['AbilityName'] .
         ' -</td></tr><tr><td class="alt3 ability">' . 
         $ability['AbilityText'] . '</td></tr>');
  }
  echo('<tr><td class="small inv">' . $row['Release'] . ' - ' . 
       $row['SetNumber'] . '/' . $row['NumberOfUnits'] . ' - ' . 
       $row['Rarity'] . '</td></tr></table>');
  if($row['Lore'] != NULL)
  {
    echo('<table width="450px"><tr><td class="alt2 small">Lore -</td></tr>
          <td class="alt3 ability">' . $row['Lore'] . '</td></tr></table>');
  }
  if($row['Notes'] != NULL)
  {
    echo('<table width="450px"><tr><td class="alt2 small">Notes -</td></tr>
          <td class="alt3 ability">' . $row['Notes'] . '</td></tr></table>');
  }
  include("./inc/footer.inc");
?>
