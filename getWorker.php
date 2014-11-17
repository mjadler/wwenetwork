<?php require_once('../Connections/wweDB.php'); ?>
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

// Get the list of matches connected to this wrestler in chronological order and using a series of key words to eliminate any milestone spoilers from the result list

$thisWorker_getWorkerMatches = "4";
if (isset($_GET['workerID'])) {
    $thisWorker_getWorkerMatches = $_GET['workerID'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getWorkerMatches = sprintf("SELECT * FROM milestones WHERE milestones.workerID = %s AND INSTR(milestones.msTitle,'vs') OR milestones.workerID = %s AND INSTR(milestones.msTitle,' v ') OR milestones.workerID = %s AND INSTR(milestones.msTitle,' royal ') OR milestones.workerID = %s AND INSTR(milestones.msTitle,' and more') ORDER BY milestones.msDate ASC", GetSQLValueString($thisWorker_getWorkerMatches, "int"),GetSQLValueString($thisWorker_getWorkerMatches, "int"),GetSQLValueString($thisWorker_getWorkerMatches, "int"),GetSQLValueString($thisWorker_getWorkerMatches, "int"));
$getWorkerMatches = mysql_query($query_getWorkerMatches, $wweDB) or die(mysql_error());
$row_getWorkerMatches = mysql_fetch_assoc($getWorkerMatches);
$totalRows_getWorkerMatches = mysql_num_rows($getWorkerMatches);

// Get all of the information for the wrestler based on the supplied URL variable

$thisWorker_getWorkerInfo = "4";
if (isset($_GET['workerID'])) {
    $thisWorker_getWorkerInfo = $_GET['workerID'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getWorkerInfo = sprintf("SELECT * FROM workers WHERE workers.workerID = %s", GetSQLValueString($thisWorker_getWorkerInfo, "int"));
$getWorkerInfo = mysql_query($query_getWorkerInfo, $wweDB) or die(mysql_error());
$row_getWorkerInfo = mysql_fetch_assoc($getWorkerInfo);
$totalRows_getWorkerInfo = mysql_num_rows($getWorkerInfo);


// Get profiles of other wrestlers who have the same workerImage as this wrestler, which is a simple way of connecting multiple wrestlers who have performed under multiple names

$thisWrestler_getOtherProfiles = "1402";
if (isset($_GET['workerID'])) {
    $thisWrestler_getOtherProfiles = $_GET['workerID'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getOtherProfiles = sprintf("SELECT workers.* FROM workers WHERE workerID != %s AND workers.workerImage != '' AND workerImage = ( SELECT workerImage FROM workers WHERE workerID = %s)", GetSQLValueString($thisWrestler_getOtherProfiles, "int"),GetSQLValueString($thisWrestler_getOtherProfiles, "int"));
$getOtherProfiles = mysql_query($query_getOtherProfiles, $wweDB) or die(mysql_error());
$row_getOtherProfiles = mysql_fetch_assoc($getOtherProfiles);
$totalRows_getOtherProfiles = mysql_num_rows($getOtherProfiles);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $row_getWorkerInfo['workerName']; ?></title>
    <link href="css/playlist_builder.css" rel="stylesheet" type="text/css">
</head>

<body>

<!-- The wrestler_info div contains all of the basic information on this wrestler -->
<div id="wrestler_info"> <span class="headline"><?php echo $row_getWorkerInfo['workerName']; ?></span>
    <?php if($row_getWorkerInfo['workerImage']!=''){ ?><br /><img name="workerImage" src="workers/<?php echo $row_getWorkerInfo['workerImage']; ?>" alt=""><?php }; ?>
    
  <!-- The rivals_link div contains a link to the getRivals page and also links back to any other related wrestler profile pages -->
    <div id="rivals_link">[<a href="getRivals.php?workerID=<?php echo $row_getWorkerInfo['workerID']; ?>">PARTNERS &amp; RIVALS</a>] <br />
        <?php if ($totalRows_getOtherProfiles > 0) { // Show if recordset not empty ?>
            <span class="smalltext">See Also: </span><br />
            <?php do { ?>
                <a href="getWorker.php?workerID=<?php echo $row_getOtherProfiles['workerID']; ?>"><?php echo $row_getOtherProfiles['workerName']; ?></a><br />
            <?php } while ($row_getOtherProfiles = mysql_fetch_assoc($getOtherProfiles)); ?>
        <?php } // Show if recordset not empty ?>
    </div>
</div>

<!-- The matchTitle div contains the list of matches for this wrestler -->
<div class="matchTitle" id="match_list">
    <?php if ($totalRows_getWorkerMatches > 0) { // Show if recordset not empty ?>
        <?php do { ?><a name="<?php echo $row_getWorkerMatches['msID']; ?>" id="<?php echo $row_getWorkerMatches['msID']; ?>"></a>            
        <a href="<?php echo $row_getWorkerMatches['msMatchLink']; ?>" class="matchTitle"><img src="icons/watch.jpg" width="15" height="15" alt="Watch" /></a>

            <a href="getWorker.php?workerID=<?php echo $row_getWorkerInfo['workerID']; ?>&amp;addToPlaylist=<?php echo $row_getWorkerMatches['msID']; ?>#<?php echo $row_getWorkerMatches['msID']; ?>"><img src="icons/add.jpg" width="15" height="15" alt="Add To Playlist" /></a>

            <span class="matchTitle"><?php echo $row_getWorkerMatches['msTitle']; ?></span><span class="matchCategory">
<?php // ADD TO PLAYLIST BUTTON STARTS HERE?>
                <?php if($_GET['addToPlaylist']==$row_getWorkerMatches['msID']){?>
            <br>
                    <?php

                    //iframe that retrieves a users playlists
                    echo "<iframe src=myPlaylists.php?url=". $row_getWorkerMatches['msMatchLink'] ."&type=milestone scrolling=no height=30 width=500 frameborder=0 name='loginFrame'></iframe>";
                    ?>
                <?php } //END OF ADD TO PLAYLIST BUTTON ?>
                <br />
<a href="getCategory.php?category=<?php echo $row_getWorkerMatches['msCategory']; ?>" class="matchCategory"><?php echo $row_getWorkerMatches['msCategory']; ?></a> | 
      <a href="getEventListing.php?msEvent=<?php echo $row_getWorkerMatches['msEventLink']; ?>"><?php echo $row_getWorkerMatches['msEvent']; ?></a> |
                <?php echo $row_getWorkerMatches['msDate']; ?></span><br />
            <br />
        <?php } while ($row_getWorkerMatches = mysql_fetch_assoc($getWorkerMatches)); ?>

        <br />
    <?php } // Show if recordset not empty ?>
</div>
</body>
</html>
<?php
mysql_free_result($getWorkerMatches);

mysql_free_result($getWorkerInfo);

mysql_free_result($getOtherProfiles);
?>
