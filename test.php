<?php

// LDAP variables
$ldaphost = "192.168.1.2";  // your ldap servers
$ldapport = 389;                 // your ldap server's port number
$ldaprdn  = 'zul';     // ldap rdn or dn
$ldappass = 'xxxxxxxxxxxxxxx';  // associated password

// Connecting to LDAP
$ldapconn = ldap_connect("192.168.1.2", 389)
          or die("Could not connect to $ldaphost");
		  
  
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

if(isset($_POST['username']) && isset($_POST['password'])) {
	if ($ldapconn) {

		// binding to ldap server
		
		
		try {
		  $ldapbind = @ldap_bind($ldapconn, "OFFICE\\".$_POST['username'], $_POST['password']);
		}

		//catch exception
		catch(Exception $e) {
		  echo 'Message: ' .$e->getMessage();
		}

		// verify binding
		if ($ldapbind) {
			echo "LDAP bind successful...";
		} else {
			echo "LDAP bind failed...";
		}

	}
}

?>

<form method="post" action="">
AD : <input type="text" name="username"/> <br>
Password : <input type="password" name="password"/> <br/>
<input type="submit" value="Check"/>
</form>