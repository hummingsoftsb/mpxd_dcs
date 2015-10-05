<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



function notification_base($logo, $user, $text) {
return '<html xmlns="http://www.w3.org/1999/xhtml"><head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>DCS Notification</title>
      <style type="text/css">
		body {
			background: #f0f0f0;
		}
		
		.btn {
			color: #ffffff;
			background-color: #008cba;
			border-color: #0079a1;
			display: inline-block;
			margin-bottom: 0;
			font-weight: normal;
			text-align: center;
			vertical-align: middle;
			cursor: pointer;
			background-image: none;
			border: 1px solid transparent;
			white-space: nowrap;
			padding: 8px 20px;
			font-size: 1.6em;
			line-height: 1.42857143;
			border-radius: 0;
			text-decoration:none;
		}
		
		.logotext {
			font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif;
			margin-left:10px; 
			color:white;
			font-size: 1.4em;
		}
		
		@media screen and (min-width:320px) and (max-width: 568px) {
			.logotext {font-size: 1.3em;}
		}
		
		@media screen and (min-width:569px) {
			.logotext {font-size: 1.8em;}
		}
		
		p {
			font-size:1.3em;
		}
      </style>

      
   </head>
<body style="padding:20px">
	<div style="min-width:550px; max-width:75%; background:white;margin:auto;">
		<div style="height:37px; background:#173F76; padding:10px">
			<table><tbody><tr><td><img src="'.$logo.'" alt="DCS Logo" style="width:70px"/></td><td style="line-height:30px">
			<span class="logotext">MPXD Data Capture System - Notification</span></td></tr></tbody></table>
		</div>
		<div style="padding:30px">
			<h1>Hi '.$user.',</h1>
			<br/>
			'.$text.'
		</div>
		
	</div>
	<div style="min-width:550px; max-width:75%;margin:auto;margin-top:10px;">
	<div style="position:relative"><div style="position:absolute;right:0px"><p style="text-align:left; font-size:0.8em; color:#888;">This email is auto-generated by MPXD DCS system. If you have any feedback, please contact the respective system administrator.</p></div></div>
	</div>
</body>

</html>';
}


function notification_data_entry_published_progressive($logo, $user, $journal, $submitter, $urlid) {
	$url = base_url();
	$text = '<p>Data entry for <strong>'.$journal.'</strong> has been submitted by '.$submitter.' <strong>for approval</strong>.</p>
			<p style="text-align:center;margin-top:40px;"><a href="'.$url.'journalvalidationview?id='.$urlid.'" class="btn">Go to validation</a></p>';
	return notification_base($logo, $user, $text);
}

function notification_data_entry_accepted_progressive($logo, $user, $journal) {
	$text = '<p>Data entry for <strong>'.$journal.'</strong> has been <strong>accepted</strong>.</p>';
	return notification_base($logo, $user, $text);
}

function notification_data_entry_rejected_progressive($logo, $user, $journal, $urlid) {
	$url = base_url();
	$text = '<p>Data entry for <strong>'.$journal.'</strong> has been <strong>rejected</strong>.</p>
			<p style="text-align:center;margin-top:40px;"><a href="'.$url.'journaldataentryadd?jid='.$urlid.'" class="btn">Go to data entry</a></p>';
	return notification_base($logo, $user, $text);
}

function notification_data_entry_published_nonprogressive($logo, $user, $journal, $submitter, $urlid) {
	$url = base_url()."index.php/";
	$text = '<p>Data entry for <strong>'.$journal.'</strong> has been submitted by '.$submitter.' <strong>for approval</strong>.</p>
			<p style="text-align:center;margin-top:40px;"><a href="'.$url.'ilyasvalidate?jid='.$urlid.'" class="btn">Go to validation</a></p>';
	return notification_base($logo, $user, $text);
}

function notification_data_entry_accepted_nonprogressive($logo, $user, $journal) {
	$text = '<p>Data entry for <strong>'.$journal.'</strong> has been <strong>accepted</strong>.</p>';
	return notification_base($logo, $user, $text);
}

function notification_data_entry_rejected_nonprogressive($logo, $user, $journal, $urlid) {
	$url = base_url()."index.php/";
	$text = '<p>Data entry for <strong>'.$journal.'</strong> has been <strong>rejected</strong>.</p>
			<p style="text-align:center;margin-top:40px;"><a href="'.$url.'ilyas?jid='.$urlid.'" class="btn">Go to data entry</a></p>';
	return notification_base($logo, $user, $text);
}

function notification_add_new_user($logo, $user, $username, $email, $password) {
	$url = base_url()."index.php/";
	$text = '<p>Your login detail are as follows:<br><br><b>URL</b> :'.base_url().' <br><b>Username</b> : '.$username.'<br><b>Email</b> : '.$email.'<br><b>Password</b> : '.$password.'</p>';
	return notification_base($logo, $user, $text);
}

function notification_change_password($logo,$user,$password){
	$text = '<p>Your password has been changed to: '.$password.'</p>';
	return notification_base($logo, $user, $text);
}

function notification_reset_password($logo, $user, $username, $email, $code) {
	$url = base_url()."index.php/";
	$text = '<p>Please click the following link in order to reset your password <a href='.base_url().'resetpassword?qcode='.$code.'>Click Here</a></p>';
	return notification_base($logo, $user, $text);
}

function notification_data_entry_assigned_nonprogressive($logo, $user, $journalname, $jid) {
	$url = base_url()."index.php/journaldataentry";
	$text = '<p>A journal has been assigned to you. Please click on the following link to continue. <a href='.$url.'>Click Here</a></p>';
	return notification_base($logo, $user, $text);
}