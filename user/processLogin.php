<?php

/**
 * File: processLogin.php
 * Date: 2018-05-19
 * Desc: Processes a login from an user.
 */

// Util (autoloader)
include_once "../utils/util.php";

// JWT Helper
require_once "../vendor/jwt_helper.php";
// Response array, set to negative values at first.
$response = array(
	"status"  => "error",
	"message" => "error",
);

/**
 * Checks if an e-mail exists in the database and returns a bool.
 *
 * @param $email string The e-m(256) NOT Nail to search for.
 *
 * @return bool If the email exists in the database.
 */
function emailExists($email): bool{
	$user = DbManager::getUserByAttribute($email);
	if (!empty($user)){
		return true;
	}

	return false;
}

/**
 * This function checks whether or not the supplied password
 * matches the one for the user in the database with password_verify().
 *
 * @param $email    string User email. Used in select.
 * @param $password string Password that is compared with password_verify() against database hash.
 *
 * @return bool If the password existed/verified.
 */
function passwordVerifies($email, $password): bool{
	// Query for the password hash for the email
	$user = DbManager::getUserByAttribute($email);
	$passwordHash = $user["password"];

	// If a password does not exist for the user, simply return false.
	if ($passwordHash === null){
		return false;
	}

	// Returns the result of the hash verification
	return password_verify($password, $passwordHash);
}

/**
 * Queries the username for the user with a certain e-mail.
 * Pre-condition is that the user's e-mail has been verified.
 *
 * @param $email string The user's e-mail.
 *
 * @return string The username of the user.
 */
function getUsernameForEmail($email): string{
	// Query for the username with the e-mail
	$user = DbManager::getUserByAttribute($email);
	
	if (!empty($user)){
		return $user["username"];
	}

	return null;

}
function isTimedOut($email): bool {
	$query = "SELECT * FROM securitylab.loginattempts WHERE email = $1";
	$response = Database::getInstance()->doParamQuery($query, array($email));
	if($response) {
		$attempts = pg_fetch_all($response);
		$earliest;
		if(count($attempts)>2) {
			foreach($attempts as $attempt) {
				if(isset($earliest)) {
					if($earliest["attempttime"] > $attempt["attempttime"]) $earliest = $attempt; 
				}
				else $earliest = $attempt;
			}
			return $earliest["attempttime"] > (time()-60);
		}
	}
	return false;
}
function failedAttempt($email): string {
	$query = "SELECT * FROM securitylab.loginattempts WHERE email = $1";
	$response = Database::getInstance()->doParamQuery($query, array($email));
	if($response) {
		$attempts = pg_fetch_all($response);
		$earliest;
		if(count($attempts)>2) {
			$time = time();
			foreach($attempts as $attempt) {
				if(isset($earliest)) {
					if($earliest["attempttime"] > $attempt["attempttime"]) $earliest = $attempt;
					if($time == $attempt["attempttime"]) $time++;
				}
				else $earliest = $attempt;
			}
			$query2 = "UPDATE securitylab.loginattempts SET attempttime = $1 WHERE email = $2 AND attempttime = $3";
			Database::getInstance()->doParamQuery($query2, array($time, $email, $earliest['attempttime']));
		} 
		else if(count($attempts) < 3 && count($attempts) >= 0) {
			$time = time();
			foreach($attempts as $attempt) {
				if($time == $attempt["attempttime"]) $time++;
			}
			$query2 = "INSERT INTO  securitylab.loginattempts (email, attempttime) VALUES($1,$2)";
			Database::getInstance()->doParamQuery($query2, array($email, $time));
		}
		else {
			$query2 = "INSERT INTO  securitylab.loginattempts (email, attempttime) VALUES($1,$2)";
			Database::getInstance()->doParamQuery($query2, array($email, time()));
		}
	}
	return "Failed login!";
}

// Checks that request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST"){

	// Decodes JSON from POST
	$input = json_decode(file_get_contents('php://input'));

	// Checks that required fields aren't empty
	if (!empty($input->email) && !empty($input->password)){

		// Checks that e-mail has correct format
		$emailRegex = "/[\w]+@[\w]+\.[a-zA-Z]+/";
		if (preg_match($emailRegex, $input->email)){

			// Sets the values to variables
			$email = htmlspecialchars($input->email);
			$password = htmlspecialchars($input->password);

			// Queries the database to see if both are valid
			// Both queries are done regardless if the e-mail exists to avoid timing attacks
			$emailResult = emailExists($email);
			$passwordResult = passwordVerifies($email, $password);
            $userIsVerified = DbManager::isUserVerified($email);

			// Login succeeded-path
			if ($emailResult){
				if(!isTimedOut($email)) {
					if($passwordResult) {
	
						if ($userIsVerified){
								// Create and give the user a JWT (token) as a session var
								$token = array();
								$token["username"] = getUsernameForEmail($email);
								setcookie("logged_in", JWT::encode($token,
										Config::getInstance()->getSetting("JWTSecretKey")),
										0, "/", "", false, true);

								// Set user messages
								$response["message"] = "Login succeeded.";
								$response["status"] = "success";
						}

						else {
								$response["message"] = "Account not verified.";
						}
					} else {
						$response["message"] = failedAttempt($email);
					}
				} else {
					$response["message"] = "You are timed out";
				}

			}// Login failed-path
			else{
				$response["message"] = "Login failed.";
			}
		}else{
			$response["message"] = "Wrong format on e-mail.";
		}
	}else{
		$response["message"] = "One or more fields were empty.";
	}

}

// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);