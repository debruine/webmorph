<?php

// register a new user and send the email

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

$return = array(
	'error' => false,
	'errorText' => '',
	'user' => '',
	'post' => $_POST
);

$email = my_clean($_POST['email']);

if (empty($email)) {
	$return['error'] = true;
	$return['errorText'] .= '<li>' .$email . ' is an invalid email address</li>';
} else if ($_POST['invite'] !== 'faces94') {		// check invite
	$return['error'] = true;
	$return['errorText'] .=  '<li>Your invitation code is not valid. Access to online psychomorph is currently restricted. Ask Lisa for an invite code if you would like to be an alpha tester.</li>';	
} else {
	// check if email is already in use
	$q = new myQuery("SELECT id FROM user WHERE LCASE(email)=LCASE('$email')");
	if ($q->get_num_rows() == 1) {
		$return['error'] = true;
		$return['errorText'] .=  '<li>A user with the email address &ldquo;' . $email . '&rdquo; already exists.</li>';
	} else {
		// register the new user!
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789";
		$password = substr(str_shuffle($chars),0,10);
		$salt = '$2y$10$' . substr(md5(microtime()), 0, 21) . '$';
		$hash = crypt($password, $salt);
		
		$firstname = my_clean($_POST['firstname']);
		$lastname = my_clean($_POST['lastname']);
		$org = my_clean($_POST['org']);
		$sex = (in_array($_POST['sex'], array('male', 'female', 'other'))) ? $_POST['sex'] : 'NULL';
		$research = ($_POST['research'] == 'true') ? 1 : 0;
		$business = ($_POST['business'] == 'true') ? 1 : 0;
		$personal = ($_POST['personal'] == 'true') ? 1 : 0;
		$school = ($_POST['school'] == 'true') ? 1 : 0;
		$art = ($_POST['art'] == 'true') ? 1 : 0;
					
		$q = new myQuery("INSERT INTO user 
			(email, password, firstname, lastname, organisation, sex, research, business, personal, art, school, regdate) 
			VALUES ('$email', '$hash', '$firstname', '$lastname', '$org', '$sex', $research, $business, $personal, $art, $school, NOW())");
		
		date_default_timezone_set('Europe/London');
		include DOC_ROOT . '/include/classes/PHPMailer/PHPMailerAutoload.php';
		
		// mail pasword to user
		$message = 	"<html><body style='color: rgb(50,50,50); font-family:\"Lucida Grande\"';>" .
					"<p>Hi $firstname $lastname,</p>\n" .
					"<p>You (or someone) just created an account at " . 
					"<a href='http://psychomorph.facelab.org'>Online Psychomorph</a>.</p>\n" .
					"<div style='border: 3px solid hsl(200,100%,30%); " . 
					"	box-shadow: 2px 2px 4px rgba(0,0,0,0.5);border-radius: 1em; padding: 1em; " . 
					"	text-align: center; width: 18em; margin: auto;'>\n" .
					"		Your new password:\n" . 
					"		<div style='font-size: 200%; margin-top: 0.5em;'>$password</div>\n" . 
					"</div>\n" .
					"<p>You can reset your password after logging in by going to the Preferences menu option.</p>\n" .
					"<p>Kind regards,</p>\n" .
					"<p>Lisa DeBruine</p>\n" .
					"</body></html>\n.";
		
		$text_message = "Hi  $firstname $lastname,\n" .
						"You (or someone) just created an account at <a href='http://psychomorph.facelab.org'>Online Psychomorph</a>.\n\n" .
						"Your new password: $password \n\n" . 
						"You can reset your password after logging in by going to the Preferences menu option.</p>\n\n" .
						"Kind regards,\n" .
						"Lisa DeBruine\n.";
	
		$mail = new PHPMailer();	//Create a new PHPMailer instance
		/*
		$mail->isSMTP();			//Tell PHPMailer to use SMTP
		$mail->SMTPDebug = 0;		//Enable SMTP debugging 0=off, 1=client, 2=server
		$mail->Debugoutput = 'html'; //Ask for HTML-friendly debug output
		$mail->Host = "mail.psy.gla.ac.uk";	//Set the hostname of the mail server
		$mail->Port = 25;			//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->SMTPAuth = false;	//Whether to use SMTP authentication
		*/
		$mail->setFrom('lisa.debruine@glasgow.ac.uk', 'Lisa DeBruine');
		//$mail->addReplyTo('lisa.debruine@glasgow.ac.uk', 'Lisa DeBruine');
		$mail->addAddress($email, $email);
		$mail->Subject = 'Psychomorph.facelab.org password change';
		$mail->msgHTML($message);
		$mail->AltBody = $text_message;
		
		//send the message, check for errors
		if (!$mail->send()) {
		   $return['error'] = true;
		   $return['errorText'] = $mail->ErrorInfo;
		} else {
		    $return['error'] = false;
		}
	}
}

scriptReturn($return);

exit;

/*

CREATE TABLE user (
	id INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	email VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	firstname VARCHAR(255),
	lastname VARCHAR(255),
	organisation VARCHAR(255),
	sex enum('female','male','other'),
	research BOOL,
	personal BOOL,
	business BOOL,
	school BOOL,
	art BOOL,
	regdate DATETIME,
	PRIMARY KEY (id),
	UNIQUE INDEX (email)
);

*/

?>