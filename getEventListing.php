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

// Retrieve all milestone links that fall under this event and match a series of keywords in an attempt to sidestep spoiler milestones that could give away the results of a match

$thisEvent_getEventListing = "http://network.wwe.com/video/v31345263/";
if (isset($_GET['msEvent'])) {
    $thisEvent_getEventListing = $_GET['msEvent'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getEventListing = sprintf("SELECT * FROM milestones WHERE (INSTR(msEventLink, %s) AND INSTR(msTitle, 'vs')) OR (INSTR(msEventLink, %s)  AND INSTR(msTitle, 'royal')) OR (INSTR(msEventLink, %s) AND INSTR(msTitle, 'etc')) OR (INSTR(msEventLink, %s) AND INSTR(msTitle, ' v ')) OR (INSTR(msEventLink, %s) AND INSTR(msTitle, 'and more')) OR (INSTR(msEventLink, %s) AND INSTR(msTitle, 'survivor series')) GROUP BY msTitle ORDER BY msMatchLink ASC", GetSQLValueString($thisEvent_getEventListing, "text"),GetSQLValueString($thisEvent_getEventListing, "text"),GetSQLValueString($thisEvent_getEventListing, "text"),GetSQLValueString($thisEvent_getEventListing, "text"),GetSQLValueString($thisEvent_getEventListing, "text"),GetSQLValueString($thisEvent_getEventListing, "text"));
$getEventListing = mysql_query($query_getEventListing, $wweDB) or die(mysql_error());
$row_getEventListing = mysql_fetch_assoc($getEventListing);
$totalRows_getEventListing = mysql_num_rows($getEventListing);


// Get all of the related information for the event that has the URL info supplied by the URL 

$thisEvent_getEventInfo = "http://network.wwe.com/video/v31326687";
if (isset($_GET['msEvent'] )) {
    $thisEvent_getEventInfo = $_GET['msEvent'] ;
}
mysql_select_db($database_wweDB, $wweDB);
$query_getEventInfo = sprintf("SELECT * FROM network WHERE INSTR(%s, network.url)", GetSQLValueString($thisEvent_getEventInfo, "text"));
$getEventInfo = mysql_query($query_getEventInfo, $wweDB) or die(mysql_error());
$row_getEventInfo = mysql_fetch_assoc($getEventInfo);
$totalRows_getEventInfo = mysql_num_rows($getEventInfo);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $row_getEventListing['msEvent']; ?></title>
    <link href="css/playlist_builder.css" rel="stylesheet" type="text/css" />
</head>
<body>


<!-- The event_info div has all of the basic information for this event -->
<div id="event_info">
    <?php if ($totalRows_getEventInfo > 0) { // Show if recordset not empty ?>
        <strong>Event Info:</strong><br />
        <span class="matchTitle"><a href="<?php echo $row_getEventInfo['url']; ?>"><img src="icons/watch.jpg" width="15" height="15" /></a> <?php echo $row_getEventInfo['name']; ?></span><span class="matchCategory"><br />
        <a href="getCategory.php?category=<?php echo $row_getEventInfo['category']; ?>" class="matchCategory"><?php echo $row_getEventInfo['category']; ?></a> | <?php echo $row_getEventInfo['showDate']; ?>
        <?php if($row_getEventInfo['rating']!=''){?><br />
            </span><span class="smalltext">Rating:
    </span><span class="nav"><?php echo $row_getEventInfo['rating']; ?></span>
        <?php } ?>
    <?php } // Show if recordset not empty ?>
    <br />
    <br />
    
    
    <!-- The get_by_date include retrieves the event before and after this event, so the user can continue going through events chronologically -->
	<?php include("fun_get_by_date.php"); ?>
</div>

<!-- The event_match_list div contains all of the milestone links that are contained under this event -->
<div id="event_match_list">
    <?php if ($totalRows_getEventListing == 0) { // Show if recordset empty ?>
        <span class="nav">There are currently no milestone markers for this event.<br />
      </span>
    <?php } else { // either show the milestone list is empty or show the entire list of matches ?>

        <strong>Match Listing: </strong><br />
        <?php do { ?>
            <a href="<?php echo $row_getEventListing['msMatchLink']; ?>"><img src="icons/watch.jpg" alt="" width="15" height="15" /></a>
            <a href="<?php echo $row_getEventListing['msMatchLink']; ?>"></a>
            <a href="getEventListing.php?msEvent=<?php echo $row_getEventListing['msEventLink']; ?>&amp;addToPlaylist=<?php echo $row_getEventListing['msID']; ?>"><img src="icons/add.jpg" width="15" height="15" /></a>
            <span class="matchTitle"><?php echo $row_getEventListing['msTitle']; ?></span>
            
            <?php // ADD TO PLAYLIST SELECTION STARTS HERE?>
            <?php if($_GET['addToPlaylist']==$row_getEventListing['msID']){?><br />
                <?php
                //iframe that retrieves a users playlists
                echo "<iframe src=myPlaylists.php?url=". $row_getEventListing['msMatchLink'] ."&type=milestone scrolling=no height=30 width=400 frameborder=0 name='loginFrame'></iframe>";
                ?>
            <?php } //END OF ADD TO PLAYLIST BUTTON?>
            <br />
        <?php } while ($row_getEventListing = mysql_fetch_assoc($getEventListing)); ?>
    <?php } // Show if recordset not empty ?> <br /><br /></div>

<!-- The workers_list div contains an include that retrieves all of the wrestlers who have milestone links connected to this event -->
<div id="workers_list">
    <?php include("fun_eventWorkers.php"); ?>
</div>
</body>
</html>
<?php
mysql_free_result($getEventListing);

mysql_free_result($getEventInfo);
?>
