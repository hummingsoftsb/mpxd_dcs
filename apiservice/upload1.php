<?php
header("Access-Control-Allow-Origin: *");
$db = pg_connect('host=localhost dbname=pilot_db_new user=postgres password=mrt@mpxd!@#123');
//$db = pg_connect('host=localhost dbname=pilotdbnew user=postgres password=summersoft123');
$id=$_GET['dataentry'];
$userid=$_GET['userid'];
$fname1=$_GET['fname2'];
if (!is_dir('../journalimagenonp')) {
mkdir('../journalimagenonp', 0777, true);
}
if (!is_dir('../journalimagenonp/'.$id)) {
mkdir('../journalimagenonp/'.$id, 0777, true);
}
if (!is_dir('../journalimagenonp/'.$id.'/'.$userid)) {
mkdir('../journalimagenonp/'.$id.'/'.$userid, 0777, true);
}



chdir('../journalimagenonp');
$rootpath=getcwd();

$dest=$rootpath."\\".$id."\\".$userid."\\".$fname1;
move_uploaded_file($_FILES["file"]["tmp_name"],$dest );

?>