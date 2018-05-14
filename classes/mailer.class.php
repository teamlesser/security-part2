<?php
/*******************************************************************************
 * Security Lab, Kurs: DT161G
 * File: mailer.class.php
 * Desc: Mailer class
 *		 Allows you to send different kind of emails to the user
 ******************************************************************************/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'PHPMailer.php';
require_once 'SMTP.php';

class Mailer {

	$mail = new PHPMailer();

	function __construct() {
		
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
		
		// Insert below values in the config file once we have one and later fetch them from there.
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 587; // or 587
		$mail->IsHTML(true);
		$mail->Username = "rovi16011@gmail.com";
		$mail->Password = "<mypass>";
		$mail->SetFrom("rovi16011@gmail.com");
		 
	}
	
	public static function sendRegistrationEmail($to, $registrationCode) {
		
		$mail->Subject = "Registration Email";
		$mail->Body =  "<html>
							<h3>Welcome " . $to . "!</h3>
							<p>In order to verify your account, please click the following link:</p><br/>
							<a href='http://localhost/register?code='" . $registrationCode . "'>Click here to verify your registration</a>
						</html>";
		
		$mail->AddAddress($to);
		
		if(!$mail->Send()) {
			error_log("Mailer Error: " . $mail->ErrorInfo);
		} else {
			echo "An registration mail has been sent to " . $to;
		}	
	}
	
	public static function sendResetPasswordEmail($to, $resetPasswordCode) {
		
		$mail->Subject = "Reset Password";
		$mail->Body =  "<html>
							<h3>Hello " . $to . "!</h3>
							<p>The link below will allow you to choose a new password:</p><br/>
							<a href='http://localhost/register?code='" . $resetPasswordCode . "'>Click here to reset your password</a>
						</html>";
		
		$mail->AddAddress($to);
		
		if(!$mail->Send()) {
			error_log("Mailer Error: " . $mail->ErrorInfo);
		} else {
			echo "A reset password mail has been sent to " . $to;
		}	
		
	}
	
	public static function sendRegistrationConfirmationEmail($to) {
		
		$mail->Subject = "Registration Complete";
		$mail->Body = "Hey " . $to . "!. Your account is now activated and you can log in here: <a href='http://localhost/login'>Click here to login</a>";
		$mail->AddAddress($to);
		
		if(!$mail->Send()) {
			error_log("Mailer Error: " . $mail->ErrorInfo);
		} else {
			echo "An registration confirmation mail has been sent to " . $to;
		}	
		
	}
	
	public static function sendResetPasswordConfirmationEmail($to) {
		
		$mail->Subject = "Password has been changed";
		$mail->Body = "Hey " . $to . "!. Your password has been changed. You can login here: <a href='http://localhost/login'>Click here to login</a>";
		$mail->AddAddress($to);
		
		if(!$mail->Send()) {
			error_log("Mailer Error: " . $mail->ErrorInfo);
		} else {
			echo "An reset password confirmation mail has been sent to " . $to;
		}	
		
	}
	
}
?>
