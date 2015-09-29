<?php
header("Access-Control-Allow-Origin: *");
        $db = pg_connect('host=localhost dbname=pilot_db_new user=postgres password=mrt@mpxd!@#123');
        $email=$_POST['email'];
        $passtxt=$_POST['passtxt'];
        $passtxt = md5($passtxt);
        $cnt=0;
		$query = "SELECT * FROM sec_user where (email_id='".$email ."' or user_name='".$email."') and pwd_txt='".$passtxt."'";
		
        $result = pg_query($query);
        if (!$result) {
            echo "Problem with query " . $query . "<br/>";
            echo pg_last_error();
            exit();
        }

        while($myrow = pg_fetch_assoc($result)) {
        $cnt=1;
        $uid=$myrow['user_id'];
		$name=$myrow['user_full_name'];
		}
		if($cnt==0) {
		echo json_encode(array('st'=>0, 'msg' => 'Invalid username or Password'.$query));
		} else {
		echo json_encode(array('st'=>1, 'uname' => $email, 'upass' => $passtxt,'uid' => $uid,'name'=>$name));
		}
 ?>