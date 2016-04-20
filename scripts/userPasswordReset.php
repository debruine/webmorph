<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/main_func.php';

date_default_timezone_set('Europe/London');
include DOC_ROOT . '/include/classes/PHPMailer/PHPMailerAutoload.php';

$return = array(
    'error' => false,
    'errorText' => '',
);

$email = my_clean($_POST['email']);

$q = new myQuery("SELECT id, firstname, lastname FROM user WHERE LCASE(email)=LCASE('$email')");
if ($q->get_num_rows() == 1) {
    $res = $q->get_one_array();
    $id = $res['id'];
    
    // create a new password
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789123456789";
    $password = substr(str_shuffle($chars),0,10);
    $salt = '$2y$10$' . substr(md5(microtime()), 0, 21) . '$';
    $hash = crypt($password, $salt);
    $q = new myQuery("UPDATE user SET password='$hash' WHERE LCASE(email)=LCASE('$email') AND id='$id'");
    
    if (DEBUG) { $return['newpass'] = $password; } // only for debugging!!!! 
    
    // email new password to the user
    $to = $email;

    $subject = 'WebMorph.org password change';
    
    $headers = "From: lisa.debruine@glasgow.ac.uk\r\n";
    $headers .= "Reply-To: lisa.debruine@glasgow.ac.uk\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    
    $message =     "<html><body style='color: rgb(50,50,50); font-family:\"Lucida Grande\"';>" .
                "<p>Hi $email,</p>\n" .
                "<p>You (or someone) just reset your password at <a href='http://webmorph.org'>WebMorph</a>.</p>\n" .
                "<div style='border: 3px solid hsl(200,100%,20%); " . 
                "    box-shadow: 2px 2px 4px rgba(0,0,0,0.5);border-radius: 1em; padding: 1em; " . 
                "    text-align: center; width: 18em; margin: auto;'>\n" .
                "        Your new password:\n" . 
                "        <div style='font-size: 200%; margin-top: 0.5em;'>$password</div>\n" . 
                "</div>\n" .
                "<p>You can reset your password after logging in by going to the Preferences menu option.</p>\n" .
                "<p>Kind regards,</p>\n" .
                "<p>Lisa DeBruine</p>\n" .
                "</body></html>\n.";
    $text_message = "Hi $email,\n" .
                "You (or someone) just reset your password at <a href='http://webmorph.org'>WebMorph</a>.\n\n" .
                "Your new password: $password \n\n" . 
                "You can reset your password after logging in by going to the Preferences menu option.</p>\n\n" .
                "Kind regards,\n" .
                "Lisa DeBruine\n.";
    
    //mail($to, $subject, $message, $headers);

    $mail = new PHPMailer();    //Create a new PHPMailer instance
    /*
    $mail->isSMTP();            //Tell PHPMailer to use SMTP
    $mail->SMTPDebug = 0;        //Enable SMTP debugging 0=off, 1=client, 2=server
    $mail->Debugoutput = 'html'; //Ask for HTML-friendly debug output
    $mail->Host = "mail.psy.gla.ac.uk";    //Set the hostname of the mail server
    $mail->Port = 25;            //Set the SMTP port number - likely to be 25, 465 or 587
    $mail->SMTPAuth = false;    //Whether to use SMTP authentication
    */
    $mail->setFrom('lisa.debruine@glasgow.ac.uk', 'Lisa DeBruine');
    //$mail->addReplyTo('lisa.debruine@glasgow.ac.uk', 'Lisa DeBruine');
    $mail->addAddress($email, $email);
    $mail->Subject = 'WebMorph.org password change';
    $mail->msgHTML($message);
    $mail->AltBody = $text_message;
    
    //send the message, check for errors
    if (!$mail->send()) {
       $return['error'] = true;
       $return['errorText'] = $mail->ErrorInfo;
    } else {
        $return['error'] = false;
    }

} else {
    $return['error'] = true;
    $return['errorText'] = "There is no account with the email <code>{$email}</code>.";
}

scriptReturn($return);

exit;

?>