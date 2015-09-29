<?php
header("Access-Control-Allow-Origin: *");
$db = pg_connect('host=localhost dbname=pilot_db_new user=postgres password=mrt@mpxd!@#123');
$id=$_GET['dataentry'];
$userid=$_GET['userid'];
$fname1=$_GET['fname2'];
if (!is_dir('../journalimage')) {
mkdir('../journalimage', 0777, true);
}
if (!is_dir('../journalimage/'.$id)) {
mkdir('../journalimage/'.$id, 0777, true);
}
if (!is_dir('../journalimage/'.$id.'/'.$userid)) {
mkdir('../journalimage/'.$id.'/'.$userid, 0777, true);
}

chdir('../journalimage');
$rootpath=getcwd();

$dest=$rootpath."\\".$id."\\".$userid."\\".$fname1;
move_uploaded_file($_FILES["file"]["tmp_name"],$dest );

?>