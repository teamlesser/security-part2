<?php
/*******************************************************************************
 * File: processResetPassword.php
 *
 * Desc: Tries to reset the password for the user in exchange of the correct resetToken
 *
 * Date: 2018-05-08
 ******************************************************************************/
require_once "../utils/util.php";

$response = array(
	"status"  => false,
	"message" => "error",
);

/**
 * Gets the user id from the users table based on an email address
 *
 * @param $email string The e-mail to search for.
 *
 * @return bool If the email exists in the database.
 */
function getUserID($email): int{
	$user = DbManager::getUserByAttribute($email);

	return $user["id"];
}

$noMatchMessage = "Either your email or password is wrong";
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	// Create an instance of mailer
	$mailer = null;
	try{
		$mailer = new Mailer;
	}catch (\PHPMailer\PHPMailer\Exception $e){
		//Fail gracefully
	}

	// Store the post data
	$data = json_decode(file_get_contents('php://input'), true);
	$email = $data{"email"};
	$newpass1 = $data{"newpass1"};
	$newpass2 = $data{"newpass2"};
	$resetToken = $data{"token"};

	// Checks that e-mail has correct format
	$emailRegex = "/[\w]+@[\w]+\.[a-zA-Z]+/";		
	if ($newpass1 != $newpass2){ // Check if the passwords match

		$response["message"] = $noMatchMessage;
	
	}else if (!preg_match($emailRegex, $email)){ // Check if the email entered is valid

		$response["message"] = "The email entered is in wrong format";

	}else{

		$userid = getUserID($email);
		if ($userid > 0){

			if (!DbManager::resetTokenIDMatch($userid, $resetToken)){ // Check if the reset token matches the email

				$response["message"] = "The email and token you entered does not match.";

			}else{

				if (DbManager::changePassword($email, $newpass1) && $mailer !== null){ // Attempt to change the
					// email
					$response["message"] = "Your password was changed. A confirmation mail should have been sent to your email.";
					$response["success"] = true;
					$mailer->sendResetPasswordConfirmationEmail($email); // Send an confirmation email

				}else{
					$response["message"] = "Your password could not be changed. Please contact customer service.";
				}
			}

		}else{
			$response["message"] = "There are no user connected to that email";
		}
	}

	header('Content-Type: application/json');
	echo json_encode($response);
}
?>
