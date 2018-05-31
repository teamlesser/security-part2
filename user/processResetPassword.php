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
	"message" => "Either your email or password is wrong"
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

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	try {
        // Create an instance of mailer
        $mailer = new Mailer;

        // Store the post data
        $data = json_decode(file_get_contents('php://input'));
        $email = $data->email;
        $newpass1 = $data->newpass1;
        $newpass2 = $data->newpass2;
        $resetToken = $data->resettoken;

        if ($newpass1 != $newpass2){ // Check if the passwords match
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){ // Check if the email entered is valid
        } elseif (!DbManager::emailExist($email)){ // Check if the email exists
        } else {
                if (!DbManager::resetTokenEmailMatch($email, $resetToken)){ // Check if the reset token matches the email

                    $response["message"] = "Cannot authenticate reset password request.";

                } else {

                    if (DbManager::changePassword($email, $newpass1)){ // Attempt to change the password

                        $response["message"]
                            = "Your password was changed. A confirmation mail should have been sent to your email.";
                        $mailer->sendResetPasswordConfirmationEmail($email); // Send an confirmation email
                        DbManager::deleteResetToken($email);

                    } else {
                        $response["message"] = "Your password could not be changed. Please contact the admin.";
                    }
                }
            }
        }

	catch(\PHPMailer\PHPMailer\Exception $e){
		$response["message"] = "Mailer error.";
	}
}

header('Content-Type: application/json');
echo json_encode($response);
