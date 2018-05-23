<?php
/*******************************************************************************
 * File: processForgotPassword.php
 *
 * Desc: Handles the process of forgotten passwords. Will create a resetToken if none already exists and store it in the database
 * 		 aswell as sending it to the users email.
 *
 * Date: 2018-05-15
 ******************************************************************************/
require_once "../utils/util.php";
 
$response = array(
    "status" => false,
    "message" => "error"
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

// Ensure that we deal with a POST request method
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Create an instance of mailer
	$mailer = new Mailer;
	
	// Store the post data
	$data = json_decode( file_get_contents( 'php://input' ), true );
	$email = $data{"email"};
	
	// Checks that e-mail has correct format
	$emailRegex = "/[\w]+@[\w]+\.[a-zA-Z]+/";	
	
	// Check if email is valid.
	if(!preg_match($emailRegex, $email)) {	
		$response["message"] = "The email you entered was not valid. Please try again.";
	}else{
		
		$userid = getUserID($email); // Make sure the user exists
		if($userid > 0) {
		
			if(DbManager::resetTokenExist($userid) { // Make sure the user does not have a reset token already
				
				$response["message"] = "An email with a reset link has already been sent to your email.";
				
			} else {
				
				// Generate a token
				$resetToken = bin2hex(random_bytes(32));

				// Add the reset token to the users table
				if(DbManager::addResetToken($userid, $resetToken)) {

					$response["message"] = "An email with a reset link has been sent to your mail.";
					$response["status"] = true;
					$mailer->sendResetPasswordEmail($email, $resetToken);

				} else {

					$response["message"] = "Error. Something went wrong. Please retry.";

				}				
				
			}
		
		} else {
			
			$response["message"] = "There are no user connected to that email";
			
		}
		
	}
	
	header('Content-Type: application/json');
    echo json_encode($response);
}
?>















