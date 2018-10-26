<?php

// Setup the Headers
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("/home/shiba009933/chriscarini.com/includes/functions.inc.php");
//die();
/* Connect to MySQL */
$mysqlLink = connectToMySQL("organizedphotodb");

if($_REQUEST['detail'] == "yes")
{
	$sql = sprintf("SELECT count(*) FROM `photos` WHERE album LIKE '%s' GROUP BY display;",mysql_real_escape_string($_REQUEST['album']));
	$result = mysql_query($sql, $mysqlLink);	
	$row = mysql_fetch_array($result);
	$album_num_not_displayed = $row[0];
	$row = mysql_fetch_array($result);
	$album_num_not_decided = $row[0];
	$row = mysql_fetch_array($result);
	$album_num_displayed = $row[0];
}
$sql = "SELECT count(*) FROM `photos` GROUP BY display";
$result = mysql_query($sql, $mysqlLink);	
$row = mysql_fetch_array($result);
$num_not_displayed = $row[0];
$row = mysql_fetch_array($result);
$num_not_decided = $row[0];
$row = mysql_fetch_array($result);
$num_displayed = $row[0];

// Close MySQL Connection
mysql_close($mysqlLink);

if($_REQUEST['detail'] == "yes")
{
	echo sprintf("<b>Album</b><br/>For Display: %s<br/>Not Displayed: %s<br/>Not Decided: %s<br/>",$album_num_displayed,$album_num_not_displayed,$album_num_not_decided);
}
$totalPercent = round(($num_displayed/($num_displayed+$num_not_displayed+$num_not_decided)), 4)*100;
$viewedPercent = round(($num_displayed/($num_displayed+$num_not_displayed)), 4)*100;
echo sprintf("<b>ALL</b><br/>For Display: %s (%s%%,%s%%)<br/>Not Displayed: %s<br/>Not Decided: %s (%s%%)<br/><i>Updated: %s</i>",
			$num_displayed,
			round(($num_displayed/($num_displayed+$num_not_displayed)), 4)*100,
			round(($num_displayed/($num_displayed+$num_not_displayed+$num_not_decided)), 4)*100,
			$num_not_displayed,
			$num_not_decided,
			round(($num_not_decided/($num_displayed+$num_not_displayed+$num_not_decided)), 4)*100,
			date("M j, Y G:i:s")
			);
?>