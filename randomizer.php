<?php require_once('../Connections/wweDB.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php include("navigation.php"); ?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}


// This recordset retrieves a single milestone link randomly from the database and its associated wrestler. 
mysql_select_db($database_wweDB, $wweDB);
$query_Recordset1 = sprintf("SELECT * FROM milestones WHERE msID = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $wweDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
mysql_select_db($database_wweDB, $wweDB);
$query_Recordset1 = "SELECT milestones.*, workers.* FROM milestones, workers WHERE INSTR(milestones.msTitle,'vs') AND workers.workerID = milestones.workerID ORDER BY rand() LIMIT 1";
$Recordset1 = mysql_query($query_Recordset1, $wweDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WWE NETWORK RANDOM MATCH GENERATOR</title>

    <!-- This refreshes the page every 30 seconds and gives the user a new random match to watch -->
    <script type="text/JavaScript">
        function timedRefresh(timeoutPeriod) {
            setTimeout("location.reload(true);",timeoutPeriod);
        }
    </script>
    <link href="css/playlist_builder.css" rel="stylesheet" type="text/css" />
</head>

<body onload="timedRefresh(30000);">


<!-- The headline div contains the basic information for the featured wrestler of this match  -->
<div class="headline" id="wrestler_info"><?php echo $row_Recordset1['workerName']; ?><br />
    <?php if ($row_Recordset1['workerImage'] != ""){ ?>
        <a href="getWorker.php?workerID=<?php echo $row_Recordset1['workerID']; ?>"><img name="" src="workers/<?php echo $row_Recordset1['workerImage']; ?>"  alt="" /></a>
    <?php }; ?>
</div>

<!-- The match_list div contains the main information for this random match  -->
<div id="match_list"><span class="nav">Feel like watching something random on the WWE Network?<br />
    A different random match will appear every 30 seconds.<br />
    <br />
    Try watching:</span><br />
<span class="matchTitle"><?php echo $row_Recordset1['msTitle']; ?>
    |</span> <a href="<?php echo $row_Recordset1['msMatchLink']; ?>"><img src="icons/watch.jpg" width="15" height="15" alt="watch" /></a><br />

    <span class="matchCategory"><a href="getEventListing.php?msEvent=<?php echo $row_Recordset1['msEventLink']; ?>"<?php echo urlencode($row_Recordset1['msEventLink']); ?>><?php echo $row_Recordset1['msEvent']; ?></a> | <a href="getCategory.php?category=<?php echo $row_Recordset1['msCategory']; ?>"><?php echo $row_Recordset1['msCategory']; ?></a> | <?php echo $row_Recordset1['msDate']; ?> </span></div>
</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
