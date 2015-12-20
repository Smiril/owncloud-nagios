<?php
$sizeuser = "1024"; // in Gigabyte storage size
$username = "user";
$password = "pwd";
$hostname = "localhost"; 
$database = "owncloud";
$location = "/srv/www/owncloud/data/"; //Change this to the location where userdirs are located on your owncloud linux server
function formatBytes($size1, $decimals = 0){
    $unit = array(
        '0' => 'Byte',
        '1' => 'KB',
        '2' => 'MB',
        '3' => 'G',
        '4' => 'TB',
        '5' => 'PB',
        '6' => 'EB',
        '7' => 'ZB',
        '8' => 'YB'
    );

    for($i = 0; $size1 >= 1024 && $i <= count($unit); $i++){
        $size1 = $size1/1024;
    }

    return round($size1, $decimals).'';
}

$dbhandle = mysql_connect($hostname, $username, $password)
 or die("Unable to connect to MySQL");
//select a database to work with
$selected = mysql_select_db($database, $dbhandle)
  or die("Could not select owncloud");
//execute the SQL query and return records
$allusers = mysql_query("select distinct userid from oc_preferences where configkey = 'lastLogin' ;");
while ($row = mysql_fetch_array($allusers)) {
        $allus = $row{'userid'};
//check on disk to see directory size
    $dirname = $location.$allus;
    $io = popen ( '/usr/bin/du -sb ' . $dirname, 'r' );
    $size = fgets ( $io , 4096);
    $sizecurb = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
// check if user has higher then default quota
$quota = mysql_query("select configvalue from oc_preferences where configkey = 'quota' and userid = '$allus' ;");
$actquot = mysql_fetch_array($quota);
//quota prolly in GB values, for nagios i want bytes
if (strpos($actquot{'configvalue'},'GB') !== false) {
    list($sizecur, $unit) = explode(" ", $actquot{'configvalue'});
        $maxquot = $sizecur * 1024 * 1024 * 1024;
}
//user has no custom quota, use the system default of 10 GB
else {
        $maxquot = $sizeuser ; 
}
//print username, current usage in bytes, and the max available space in bytes
echo $allus."-".formatBytes($sizecurb, 3)."-".$maxquot."";
}
//close the connection
mysql_close($dbhandle);
?>
