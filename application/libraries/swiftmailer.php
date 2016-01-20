<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Swiftmailer {



    var	$smtp_host		= "smtp.gmail.com";		// SMTP Server.  Example: mail.earthlink.net
    var	$smtp_user		= "jainrose90@gmail.com";		// SMTP Username
    var	$smtp_pass		= "iamamadgirl90";		// SMTP Password
    var	$smtp_port		= "465";		// SMTP Port
    var $mpxd_logo 		= "img/logo_1.png";
    var $sender_name 	= "MPXD DCS";
	
	function __construct() {
		require_once 'application/third_party/Swift-4.1.1/lib/swift_required.php';
		require_once 'application/libraries/mailbodies.php';
		$CI =& get_instance();
		$CI->load->helper('url');
	}
	
	function send($message){ 
		//Create the Transport 
		$transport = Swift_SmtpTransport::newInstance ($this->smtp_host, $this->smtp_port, 'ssl')
		->setUsername($this->smtp_user)
		->setPassword($this->smtp_pass); 

		$mailer = Swift_Mailer::newInstance($transport); 

		$result = $mailer->send($message);
		return $result;
	}
	
	function data_entry_published_progressive($email, $gkname, $dename, $journalname, $jid) {
		$message = Swift_Message::newInstance("Notification - Data entry published")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $gkname))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_published_progressive($logo, $gkname, $journalname, $dename, $jid));
		
		return $this->send($message);
	}
	
	function data_entry_accepted_progressive($email, $name, $journalname) {
		$message = Swift_Message::newInstance("Notification - Data entry accepted")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $name))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_accepted_progressive($logo, $name, $journalname));
		
		return $this->send($message);
	}
	
	function data_entry_rejected_progressive($email, $dename, $journalname, $jid) {
		$message = Swift_Message::newInstance("Notification - Data entry rejected")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $dename))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_rejected_progressive($logo, $dename, $journalname, $jid));
		
		return $this->send($message);
	}
	
	function data_entry_published_nonprogressive($email, $gkname, $dename, $journalname, $jid) {
		$message = Swift_Message::newInstance("Notification - Data entry published")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $gkname))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_published_nonprogressive($logo, $gkname, $journalname, $dename, $jid));
		
		return $this->send($message);
	}
	
	function data_entry_accepted_nonprogressive($email, $name, $journalname) {
		$message = Swift_Message::newInstance("Notification - Data entry accepted")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $name))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_accepted_nonprogressive($logo, $name, $journalname));
		
		return $this->send($message);
	}
	
	function data_entry_rejected_nonprogressive($email, $dename, $journalname, $jid) {
		$message = Swift_Message::newInstance("Notification - Data entry rejected")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $dename))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_rejected_nonprogressive($logo, $dename, $journalname, $jid));
		
		return $this->send($message);
	}
	
	function data_entry_assigned($email, $dename, $journalname, $jid) {
		$message = Swift_Message::newInstance("Notification - Data entry assigned")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $dename))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_data_entry_assigned($logo, $dename, $journalname, $jid));
		
		return $this->send($message);
	}

    /*function to send notification email to validator. done by jane*/
	function validation_assigned($email, $validator, $journalname, $jid) {
		$message = Swift_Message::newInstance("Notification - Validation assigned")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $validator))
		->setContentType('text/html');

		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_validation_assigned($logo, $validator, $journalname, $jid));

		return $this->send($message);
	}
	
	function add_new_user($user, $username, $email, $password) {
		$message = Swift_Message::newInstance("MPXD Data Capture System Login Detail")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $user))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_add_new_user($logo, $user, $username, $email, $password));
		
		return $this->send($message);
	}
	
	function reset_password($user, $username, $email, $password) {
		$message = Swift_Message::newInstance("MPXD Data Capture System Password Reset")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $user))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_change_password($logo,$user,$password));
		
		return $this->send($message);
	}
	
	function reset_password_front($user, $username, $email, $code) {
		$message = Swift_Message::newInstance("MPXD Data Capture System Password Reset")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $user))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$message->setBody(notification_reset_password($logo, $user, $username, $email, $code));
		
		return $this->send($message);
	}
	
	function send_collective_published($email, $user, $journals) {
		$message = Swift_Message::newInstance("Notification - Data entry published")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $user))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$body = notification_collective_published($logo, $user, $journals);
		$message->setBody($body);
		$status = $this->send($message);
		return array(
			'status' => $status
		);
	}
	
	function send_collective_rejected($email, $user, $journals) {
		$message = Swift_Message::newInstance("Notification - Data entry rejected")
		->setFrom(array($this->smtp_user => $this->sender_name))
		->setTo(array($email => $user))
		->setContentType('text/html');
		
		$logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
		$body = notification_collective_rejected($logo, $user, $journals);
		$message->setBody($body);
		$status = $this->send($message);
		return array(
			'status' => $status
		);
	}

    /*function to send notification  mail for pending journals. done by jane*/
    function send_collective_pending($email, $user, $journals) {
        $message = Swift_Message::newInstance("Notification - Data entry pending")
            ->setFrom(array($this->smtp_user => $this->sender_name))
            ->setTo(array($email => $user))
            ->setContentType('text/html');

        $logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
        $body = notification_data_entry_pending($logo, $user, $journals);
        $message->setBody($body);
        $status = $this->send($message);
        return array(
            'status' => $status
        );
    }

    /*function to send reminder mail for incomplete data entry. done by jane*/
    function send_collective_reminder_incomplete($email, $user, $role, $journals) {
        $message = Swift_Message::newInstance("Reminder - Incomplete data entry")
            ->setFrom(array($this->smtp_user => $this->sender_name))
            ->setTo(array($email => $user))
            ->setContentType('text/html');

        $logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
        $body = reminder_collective_incomplete($logo, $user, $role, $journals);
        $message->setBody($body);
        $status = $this->send($message);
        return array(
            'status' => $status
        );
    }

    /*function to send reminder mail for waiting validation. done by jane*/
    function send_collective_reminder_waiting($email, $user, $role, $journals) {
        $message = Swift_Message::newInstance("Reminder - Waiting for validation")
            ->setFrom(array($this->smtp_user => $this->sender_name))
            ->setTo(array($email => $user))
            ->setContentType('text/html');

        $logo = $message->embed(Swift_Image::fromPath($this->mpxd_logo));
        $body = reminder_collective_waiting($logo, $user, $role, $journals);
        $message->setBody($body);
        $status = $this->send($message);
        return array(
            'status' => $status
        );
    }
}