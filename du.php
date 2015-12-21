<?php
$username = "user";
$password = "pwd";
$hostname = "localhost"; 
$database = "owncloud";
$location = "/srv/www/owncloud/data/"; //Change this to the location where userdirs are located on your owncloud
$user = $_GET["user"];
$userx = $location.$user;

$dbhandle = mysql_connect($hostname, $username, $password)
 or die("Unable to connect to MySQL");
//select a database to work with
$selected = mysql_select_db($database, $dbhandle)
  or die("Could not select owncloud");
// check if user has higher then default quota
$quota = mysql_query("select configvalue from oc_preferences where configkey = 'quota' and userid = '$user' ;");
$actquot = mysql_fetch_array($quota);
//check on disk to see directory size
    $dirname = $userx."/files/";
    $io = popen ( '/usr/bin/du -sb ' . $dirname, 'r' );
    $size = fgets ( $io , 4096);
    $sizecurb = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
//quota prolly in TB values, for nagios i want bytes
if (strpos($actquot{'configvalue'},'TB') !== false) {
    list($sizecur, $unit) = explode(" ", $actquot{'configvalue'});
        $maxquot = $sizecur * 1024 * 1024 * 1024 * 1024;
}
//user has no custom quota, use the system default of 10 GB
else {
        $maxquot = floor( disk_free_space($userx."/files/" ) ); 
}
//print username, current usage in bytes, and the max available space in bytes
echo $user."-".$sizecurb."-".$maxquot."";

//close the connection
mysql_close($dbhandle);
?>
