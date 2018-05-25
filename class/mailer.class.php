<?php
/*******************************************************************************
 * Security Lab, Kurs: DT161G
 * File: mailer.class.php
 * Desc: Mailer class
 *		 Allows you to send different kind of emails to the user
 ******************************************************************************/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once dirname(__DIR__). '/vendor/PHPMailer.php';
require_once dirname(__DIR__). '/vendor/SMTP.php';
require_once dirname(__DIR__). '/vendor/Exception.php';

class Mailer {

	/**
	* @var PHPMailer
	*/
	private $mail;

    /**
     * Mailer constructor. Initializes the PHPMailer with settings from Config.
     * @throws Exception PHPMailer Exception
     */
	function __construct() {

		// Creates new instance of mailer
		$mail = new PHPMailer;

		// Gets Config-instance to get settings
		$config = Config::getInstance();

		$mail->IsSMTP(); // enable SMTP

		$mail->SMTPDebug = $config->getSetting("SMTPDebug"); 	// If debugging should be on and for what
		$mail->SMTPAuth = $config->getSetting("SMTPAuth"); 		// If authentication should be enabled
		$mail->SMTPSecure = $config->getSetting("SMTPSecure"); 	// Secure transfer enabled (REQUIRED for Gmail)
		
		// Configure with settings from Config
		$mail->Host = $config->getSetting("SMTPHost");
		$mail->Port = $config->getSetting("SMTPPort");
		$mail->IsHTML($config->getSetting("SMTPIsHtml"));
		$mail->Username = $config->getSetting("SMTPUsername");
		$mail->Password = $config->getSetting("SMTPPassword");;
		$mail->SetFrom($config->getSetting("SMTPUsername"));

		// Sets the PHPMailer to this class
		$this->mail = $mail;
	}

    /**
	 * Function that sends mail with needed values for
	 * to, subject and body fields. It echoes a success message
	 * if sending worked.
     * @param $to string E-mail to send to.
     * @param $subject string Subject of the E-mail.
     * @param $body string The body of the e-mail, can be HTML.
     * @param $pageMsg string A message that is echoed from the page.
     */
    private function sendMail($to, $subject, $body, $pageMsg){
        $this->mail->Subject = $subject;
        $this->mail->Body = $body;
        $this->mail->AddAddress($to);

        try{
            if ($this->mail->Send()){
            } else {
                error_log("Mailer Error: " . $this->mail->ErrorInfo);
			}
        }

        // PHPMailer Exception
        catch(Exception $me){
            error_log("Mailer Error: " . $this->mail->ErrorInfo);
        }
    }

    /**
     * Composes and sends a registration e-mail.
     * @param $to string The e-mail to send message to.
     */
    public function sendRegistrationEmail($to, $registrationCode)
    {
        $subject = "Registration Email";
        $body = "<html>
							<h3>Welcome " . $to . "!</h3>
							<p>In order to verify your account, please click the following link:</p><br/>
							<a href='http://localhost:8080/securitylab/user/verifyUser.php?code=" . $registrationCode . "'>Click here to verify your registration</a>
							<p>Plaintext: http://localhost:8080/securitylab/user/verifyUser.php?code=$registrationCode</p>
						</html>";
        $pageMsg = "A registration mail has been sent to " . $to;

        $this->sendMail($to, $subject, $body, $pageMsg);
    }

    /**
     * Composes and sends a reset password e-mail.
     * @param $to string The e-mail to send message to.
     */
    public function sendResetPasswordEmail($to, $resetPasswordCode) {

        $subject = "Reset Password";
        $body =  "<html>
							<h3>Hello " . $to . "!</h3>
							<p>The link below will allow you to choose a new password:</p><br/>
							<a href='http://localhost:8080/securitylab/user/resetPassword.php?code=" . $resetPasswordCode . "'>Click here to reset your password</a>
							<p>Plaintext: http://localhost:8080/securitylab/user/resetPassword.php?code=$resetPasswordCode</p>
						</html>";
        $pageMsg = "A reset password mail has been sent to " . $to;

        $this->sendMail($to, $subject, $body, $pageMsg);
    }

    /**
	 * Composes and sends a registration confirmation e-mail.
     * @param $to string The e-mail to send message to.
     */
    public function sendRegistrationConfirmationEmail($to) {
        $subject = "Registration Complete";
        $body = "Hey " . $to . "!. Your account is now activated and you can log in here: <a href='http://localhost:8080/securitylab/index.php'>Click here to login</a>";
        $pageMsg = "A registration confirmation mail has been sent to " . $to;

        $this->sendMail($to, $subject, $body, $pageMsg);
    }

    /**
	 * Composes and sends a reset password confirmation e-mail.
     * @param $to string The e-mail to send message to.
     */
	public function sendResetPasswordConfirmationEmail($to) {
		$subject = "Password has been changed";
		$body = "Hey " . $to . "!. Your password has been changed. You can login here: <a href='http://localhost:8080/securitylab/index.php'>Click here to login</a>";
		$pageMsg = "A reset password confirmation mail has been sent to " . $to;

		$this->sendMail($to, $subject, $body, $pageMsg);
	}
}
?>