<?php
/*******************************************************************************
 * File: forgotpassword.php
 *
 * Desc: Handles the process of forgotten passwords. Will create a resetToken if none already exists and store it in the database
 * 		 aswell as sending it to the users email.
 *
 * Date: 2018-05-15
 ******************************************************************************/

 // Util (autoloader)
include_once "../utils/util.php";
 
$response = array(
    "status" => false,
    "message" => "error"
);

/**
 * Checks if an e-mail exists in the database and returns a bool.
 * @param $email string The e-mail to search for.
 * @return bool If the email exists in the database.
 */
function emailExists($email): bool {
    
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
* @var $email The email to check in the users table
* @returns boolean True if the reset token exists, otherwise false
*/
function resetTokenExist($email) : bool {
	
	$query = "SELECT COUNT(*) FROM securitylab.users WHERE email = $1 AND resettoken IS NOT NULL";
	$param = array($email)

	// Result only returns a boolean
	$db = Database::getInstance();
	$result = $db->doParamQuery($query, $param);

	// Check if an resetToken exists
	if($result > 0)
		return true;
	} else {
		return false;
	}
}

/**
* Makes an attempt to add a reset token to the users table
* @var $email The email to check in the users table
* @var $resetToken The reset token
* @returns boolean True if the reset token was added successfully, otherwise false
*/
function addResetToken($email, $resetToken) : bool {
	
	$date = date('Y-m-d H:i:s');
	$query = "UPDATE securitylab.users SET resettoken = $1, resettokendate = $2 WHERE email = $3";
	$param = array($resetToken, $date, $email);

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

// Ensure that we deal with a POST request method
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// Create an instance of mailer
	$mailer = new Mailer;
	
	// Store the post data
	$data = json_decode( file_get_contents( 'php://input' ), true );
	$email = $data{"email"};
   
	// Check whetever the email is valid or not
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$response["message"] = "The email you entered was not valid. Please try again.";
	} else {
	
		if(emailExist($email)) { // Check if email entered exists.
				
			if(resetTokenExist($email)) { // Check if user has any existing resettokens
				
				$response["message"] = "An email with a reset link has already been sent to your email.";			
			
			} else { 
				
				// Generate a token
				$resetToken = bin2hex(random_bytes(32));

				// Add the reset token to the users table
				if(addResetToken($email, $resetToken)) {

					$response["message"] = "An email with a reset link has been sent to your mail.";
					$response["status"] = true;
					$mailer->sendResetPasswordEmail($email, $resetToken);

				} else {

					$response["message"] = "Error. Something went wrong. Please retry.";

				}				
			}
			
		} else {
			
			$response["message"] = "The email you entered does not exist"; // Security issue to inform

		}
	}
		
	header('Content-Type: application/json');
    echo json_encode($response);
}
?>















