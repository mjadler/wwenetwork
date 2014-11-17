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

// Retrieve all milestone links that are shared by both the heel wrestler and the face wrestler that contain the key word "vs", which should eliminate all spoiler milestones from the result set

$thisFace_getRivals = "1799";
if (isset($_GET['face'])) {
    $thisFace_getRivals = $_GET['face'];
}
$thisHeel_getRivals = "1509";
if (isset($_GET['heel'])) {
    $thisHeel_getRivals = $_GET['heel'];
}

mysql_select_db($database_wweDB, $wweDB);
$query_getRivals = sprintf("SELECT *, COUNT(*) FROM milestones WHERE milestones.workerID = %s OR milestones.workerID = %s AND INSTR(msTitle, 'vs') GROUP BY msMatchLink HAVING COUNT(*)>1 ORDER BY milestones.msDate", GetSQLValueString($thisFace_getRivals, "int"),GetSQLValueString($thisHeel_getRivals, "int"));
$getRivals = mysql_query($query_getRivals, $wweDB) or die(mysql_error());
$row_getRivals = mysql_fetch_assoc($getRivals);
$totalRows_getRivals = mysql_num_rows($getRivals);


//get the wrestler identified by the getFace URL variable

$thisFace_getFace = "1799";
if (isset($_GET['face'])) {
    $thisFace_getFace = $_GET['face'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getFace = sprintf("SELECT * FROM workers WHERE workers.workerID = %s", GetSQLValueString($thisFace_getFace, "int"));
$getFace = mysql_query($query_getFace, $wweDB) or die(mysql_error());
$row_getFace = mysql_fetch_assoc($getFace);
$totalRows_getFace = mysql_num_rows($getFace);


//get the wrestler identified by the getHeel URL variable

$thisHeel_getHeel = "1509";
if (isset($_GET['heel'])) {
    $thisHeel_getHeel = $_GET['heel'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getHeel = sprintf("SELECT * FROM workers WHERE workers.workerID = %s", GetSQLValueString($thisHeel_getHeel, "int"));
$getHeel = mysql_query($query_getHeel, $wweDB) or die(mysql_error());
$row_getHeel = mysql_fetch_assoc($getHeel);
$totalRows_getHeel = mysql_num_rows($getHeel);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $row_getFace['workerName']; ?> vs. <?php echo $row_getHeel['workerName']; ?> Rivalry </title>
    <link href="css/playlist_builder.css" rel="stylesheet" type="text/css" />
</head>

<body>

<!-- Left column is the "face" column -->
<div id="rival_left"><span class="headline"><?php echo $row_getFace['workerName']; ?></span><br />
    <?php if ($row_getFace['workerImage'] != ""){ ?>
        <a href="getWorker.php?workerID=<?php echo $row_getFace['workerID']; ?>"><img src="workers/<?php echo $row_getFace['workerImage']; ?>" /></a>
    <?php }; ?>
</div>

<!-- Right column is the "heel" column -->
<div id="rival_right">
    <span class="headline"><?php echo $row_getHeel['workerName']; ?></span>
    <p>
        <?php if ($row_getHeel['workerImage'] != ""){ ?>
            <a href="getWorker.php?workerID=<?php echo $row_getHeel['workerID']; ?>"><img src="workers/<?php echo $row_getHeel['workerImage']; ?>" alt="Heel" /></a>
        <?php }; ?>
    </p>
</div>

<!-- Middle column contains a chronological listing of matches that have milestones shared by both the heel and face -->
<div class="rivalry_middle" id="rival_middle">
    <?php if ($totalRows_getRivals > 0) { // Show if recordset not empty ?>
        <?php do { ?>
            <a name="<?php echo $row_getRivals['msID']; ?>" id="<?php echo $row_getRivals['msID']; ?>"></a><a href="<?php echo $row_getRivals['msMatchLink']; ?>"><img src="icons/watch.jpg" width="15" height="15" alt="Watch" /></a>
            <a href="getRivalry.php?face=<?php echo $_GET['face']; ?>&amp;heel=<?php echo $_GET['heel']; ?>&amp;addToPlaylist=<?php echo $row_getRivals['msID']; ?>#<?php echo $row_getRivals['msID']; ?>"><img src="icons/add.jpg" width="15" height="15" alt="Add To Playlist" /></a> <span class="matchTitle"><?php echo $row_getRivals['msTitle']; ?></span> <br />
            <span class="matchCategory"> <span class="matchCategory"><a href="getCategory.php?category=<?php echo $row_getRivals['msCategory']; ?>"><?php echo $row_getRivals['msCategory']; ?></a></span> | <a href="getEventListing.php?msEvent=<?php echo $row_getRivals['msEventLink']; ?>"><?php echo $row_getRivals['msEvent']; ?></a></span> |
            <span class="matchCategory"><?php echo $row_getRivals['msDate']; ?></span>
            
            
 <!-- If "addToPlaylist" is in the url, show a pulldown menu where the user can add this match to one of their custom playlists -->
            <?php if($_GET['addToPlaylist']==$row_getRivals['msID']){?><br />

<!-- The iframe contains the myPlaylists page that allows the page to load a session locked page that retrieves playlists based on the users login info, or bounces them to a login page -->
                <?php
                //iframe that retrieves a users playlists
                echo "<iframe src=myPlaylists.php?url=". $row_getRivals['msMatchLink'] ."&type=milestone scrolling=no height=30 width=400 frameborder=0 name='loginFrame'></iframe>";
                ?>
            <?php } //END OF ADD TO PLAYLIST BUTTON?>
            <br /><br />
        <?php } while ($row_getRivals = mysql_fetch_assoc($getRivals)); ?>
        <br />
        </p>
    <?php } // Show if recordset not empty ?>
    
    <!-- Show if no matches are found for this event -->
    <?php if ($totalRows_getRivals == 0) { // Show if recordset empty ?>
        No matches found.
    <?php } // Show if recordset empty ?>
    </li>
</div>
</body>
</html>
<?php
mysql_free_result($getRivals);

mysql_free_result($getFace);

mysql_free_result($getHeel);
?>
