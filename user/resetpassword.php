<?php
/*******************************************************************************
 * File: resetpassword.php
 *
 * Desc: Tries to reset the password for the user in exchange of the correct resetToken
 *
 * Date: 2018-05-08
 ******************************************************************************/
require_once "../class/mailer.class.php";
require_once "../class/config.class.php";
require_once "../class/database.class.php";
 
$response = array(
    "status" => false,
    "message" => "error"
);

/**
 * Checks if an e-mail exists in the database and returns a bool.
 * @param $email string The e-mail to search for.
 * @return bool If the email exists in the database.
 */
function emailExist($email): bool {
    
	$query = "SELECT EXISTS (SELECT * FROM securitylab.users WHERE email = $1);";
    $param = array($email);

    // Result only returns a boolean
    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);

    // "t" is true, "f" is false
    if ($result == null || $result == "f"){
        return false;
    } else {
        return true;
    }
}

/**
 * Gets the user id from the users table based on an email address
 * @param $email string The e-mail to search for.
 * @return bool If the email exists in the database.
 */
function getUserID($email): integer {

	$query = "SELECT user_id FROM securitylab.users WHERE email = $1);";
	$param = array($email);
	
	$db = Database::getInstance();
	$result = $db->doParamQuery($query, $param);
	
	if($result == null || $result == "f")
		return 0;
	else 
		return $result;
}

/**
* Check if the email entered matches with the users reset token 
* @var $email The email to check in the users table
* @var $resetToken The reset token
* @returns boolean True if the reset token exists in the row of the user, based on his/her email
*/
function resetTokenIDMatch($userid, $resetToken) : bool {
	
	$query = "SELECT COUNT(*) FROM securitylab.reset WHERE user_id = $1 AND reset_token = $2 IS NOT NULL";
    $param = array($userid, $resetToken);

    // Result only returns a boolean
    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);

	// Check if there was a match
	if($result > 0) {
		return true;
	} else {
		return false;
	}
}

/**
* Makes an attempt to change a users password
* @var $email The email to check in the users table
* @var $newPassword The new password to change to
* @returns boolean True if password was changed, otherwise false
*/
function changePassword($email, $newPassword) : bool {
	
	$query = "UPDATE securitylab.users SET password = $1 WHERE email = $2";
    $param = array($newPassword, $email);

    // Result only returns a boolean
    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);

	// "t" is true, "f" is false
    if ($result == null || $result == "f"){
        return false;
    } else {
		deleteResetToken($email);
        return true;
    }
}

/**
 * Delets a reset token from a users table
 * @var $email The email to check in the users table
 * @returns boolean True if it was deleted, otherwise false
 */
function deleteResetToken($userid) : void {
	
	$query = "UPDATE securitylab.reset SET reset_token = NULL, reset_token_inserted_time = NULL WHERE user_id = $1";
    $param = array($userid);

    // Result does not really need to return anything
    $db = Database::getInstance();
    $db->doParamQuery($query, $param);
}
	
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Create an instance of mailer
	$mailer = new Mailer;

	// Store the post data
    $data = json_decode( file_get_contents( 'php://input' ), true );
	$email = $data{"email"};
	$newpass1 = $data{"newpass1"};
	$newpass2 = $data{"newpass2"};
	$resetToken = $data{"token"};
	
	if($newpass1 != $newpass2) { // Check if the passwords match
		
		$response["message"] = "The passwords you entered does not match eithother";
	
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Check if the email entered is valid
		
		$response["message"] = "The email you entered is not valid.";
		
	} else if (!emailExist($email)) { // Check if the email exists
		
		$response["message"] = "The email you entered does not exist.";
	
	} else {
		
		$userid = getUserID($email);
		if($userid > 0) {
			
			if(!resetTokenIDMatch($userid, $resetToken) { // Check if the reset token matches the email
			
				$response["message"] = "The email and token you entered does not match.";
			
			} else {
				
				if(changePassword($email, $newpass1)) { // Attempt to change the email

					$response["message"] = "Your password was changed. An confirmation mail should have been sent to your email.";
					$response["success"] = true;
					$mailer->sendResetPasswordConfirmationEmail($email); // Send an confirmation email
				
				} else {

					$response["message"] = "Your password could not be changed. Please contact customer service.";
				
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















