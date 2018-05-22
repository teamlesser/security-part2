<?php
/*******************************************************************************
 * File: forgotpassword.php
 *
 * Desc: Handles the process of forgotten passwords. Will create a resetToken if none already exists and store it in the database
 * 		 aswell as sending it to the users email.
 *
 * Date: 2018-05-15
 ******************************************************************************/
require_once "../class/mailer.class.php";
require_once "../class/config.class.php";
require_once "../class/database.class.php";
 
$response = array(
    "status" => false,
    "message" => "error"
);

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
* Check if the user already have a reset token in his/her data
* @var $userid The userid to check in the reset table
* @returns boolean True if the reset token exists, otherwise false
*/
function resetTokenExist($userid) : bool {
	
	$query = "SELECT reset_token FROM securitylab.reset WHERE user_id = $1 AND reset_token IS NOT NULL";
	$param = array($userid);

	// Result only returns a boolean
	$db = Database::getInstance();
	$result = $db->doParamQuery($query, $param);
	
	// Check if an resetToken exists
    if ($result == null || $result == "f"){
        return false;
    } else {
        return true;
    }
}

/**
* Makes an attempt to add a reset token to the users table
* @var $email The email to check in the users table
* @var $resetToken The reset token
* @returns boolean True if the reset token was added successfully, otherwise false
*/
function addResetToken($userid, $resetToken) : bool {
	
	$date = date('Y-m-d H:i:s');
	$query = "UPDATE securitylab.reset SET reset_token = $1, reset_token_inserted_time = $2 WHERE user_id = $3";
	$param = array($resetToken, $date, $userid);

	// Result only returns a boolean
	$db = Database::getInstance();
	$result = $db->doParamQuery($query, $param);

	// "t" is true, "f" is false
    if ($result == null || $result == "f") {
        return false;
    } else {
        return true;
    }
}

// Ensure that we deal with a POST request method
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Create an instance of mailer
	$mailer = new Mailer;
	
	// Store the post data
	$data = json_decode( file_get_contents( 'php://input' ), true );
	$email = $data{"email"};
	
	// Check if email is valid.
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		
		$response["message"] = "The email you entered was not valid. Please try again.";
		
		if(emailExist($email)) { // Make sure the email exists
			
			$userid = getUserID($email); // Make sure the user exists
			if($userid > 0) {
			
				if(resetTokenExist($userid) { // Make sure the user does not have a reset token already
					
					$response["message"] = "An email with a reset link has already been sent to your email.";
					
				} else {
					
					// Generate a token
					$resetToken = bin2hex(random_bytes(32));

					// Add the reset token to the users table
					if(addResetToken($userid, $resetToken)) {

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
			
		} else {
			
			$response["message"] = "The email you entered does not exist"; 
			
		}
		
	}
	
	header('Content-Type: application/json');
    echo json_encode($response);
}
?>















