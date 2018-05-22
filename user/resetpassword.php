<?php
/*******************************************************************************
 * File: resetpassword.php
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
	$mailer = new Mailer;

	// Store the post data
	$data = json_decode(file_get_contents('php://input'), true);
	$email = $data{"email"};
	$newpass1 = $data{"newpass1"};
	$newpass2 = $data{"newpass2"};
	$resetToken = $data{"token"};

	if ($newpass1 != $newpass2){ // Check if the passwords match

		$response["message"] = $noMatchMessage;

	}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){ // Check if the email entered is valid

		$response["message"] = $noMatchMessage;

	}elseif (!emailExist($email)){ // Check if the email exists

		$response["message"] = $noMatchMessage;

	}else{

		$userid = getUserID($email);
		if ($userid > 0){

			if (!DbManager::resetTokenIDMatch($userid, $resetToken)){ // Check if the reset token matches
				// the email

				$response["message"] = "The email and token you entered does not match.";

			}else{

				if (DbManager::changePassword($email, $newpass1)){ // Attempt to change the email

					$response["message"]
						= "Your password was changed. A confirmation mail should have been sent to your email.";
					$response["success"] = true;
					$mailer->sendResetPasswordConfirmationEmail($email); // Send an confirmation email

				}else{
					$response["message"]
						= "Your password could not be changed. Please contact customer service.";
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
