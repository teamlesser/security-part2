<?php
/*******************************************************************************
 * File: processForgotPassword.php
 *
 * Desc: Handles the process of forgotten passwords.
 * 		 Will create a resetToken if none already exists and
 * 		 store it in the database as well as sending it to the users email.
 *
 * Date: 2018-05-15
 ******************************************************************************/
require_once "../class/mailer.class.php";
require_once "../class/config.class.php";
require_once "../class/database.class.php";
 
$response = array(
    "message" => "error"
);

// Ensure that we deal with a POST request method
if($_SERVER['REQUEST_METHOD'] == 'POST') {

	try {
        // Create an instance of mailer
        $mailer = new Mailer();

        // Store the post data
        $data = json_decode(file_get_contents('php://input'));
        $email = $data->email;

        // Check if email is valid.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != null && $email !== "") {
            $response["message"] = "The email you entered was not valid. Please try again.";
        } else {

        	// Both are checked regardless of the result of the first one (to avoid timing attack)
        	$emailExists = DbManager::emailExist($email);
        	$resetTokenExists = DbManager::resetTokenExist($email);

            if ($emailExists) { // Make sure the email exists

                if ($resetTokenExists) { // Make sure the user does not have a reset token already

                    $response["message"] = "An email with a reset link has already been sent to your email.";

                } else {

                    // Generate a token
                    $resetToken = bin2hex(random_bytes(32));
					$resetInsertionResult = DbManager::addResetToken($email, $resetToken);

                    // Add the reset token to the users table
                    if ($resetInsertionResult) {

                        $response["message"] = "We have sent an email with a reset link if your account exists.";
                        $mailer->sendResetPasswordEmail($email, $resetToken);

                    } else {

                        $response["message"] = "Error. Could not store reset entry.";

                    }
                }
            }
            else {
                    $response["message"] = "We have sent an email with a reset link if your account exists.";
            }
        }
	}

	catch(\PHPMailer\PHPMailer\Exception $e){
		$response["message"] = "Mailer error.";
	}

	catch(Exception $e){
		$response["message"] = "Token generation error.";
	}
}

header('Content-Type: application/json');
echo json_encode($response);















