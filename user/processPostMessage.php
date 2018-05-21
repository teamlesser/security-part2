<?php

/**
 * File: processPostMessage.php
 * Date: 2018-05-19
 * Desc: Processes a message being posted.
 */

// Util (autoloader)
include_once("../utils/util.php");

// JWT Helper
require_once "../vendor/jwt_helper.php";

// Response array, set to negative values at first.
$response = array(
	"status"  => "error",
	"message" => "error",
);

/**
 * Checks that all keywords in an array are shorter than 25 chars.
 *
 * @param $keywords string[] The keywords.
 *
 * @return bool If the keywords all passed.
 */
function keywordLengthCheck($keywords): bool{
	$keywordError = false;

	// Check for keyword length
	foreach ($keywords as $keyword){

		if (strlen($keyword) > 24){
			$keywordError = true;
			break;
		}
	}

	return !$keywordError;
}

// Checks that request method is POST and that user is logged in //
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_COOKIE["logged_in"])){

	// Decodes JSON from POST
	$input = json_decode(file_get_contents('php://input'));

	// Checks that required field isn't empty
	if (!empty($input->message)){

		// Sanitizes input
		$message = htmlspecialchars($input->message);
		$keywords = htmlspecialchars($input->keywords);

		// Gets JWT to check that user is really logged in
		$jwt = JWT::decode($_COOKIE["logged_in"],
			Config::getInstance()->getSetting("JWTSecretKey"));
		$username = $jwt->username;
		$usernameExists = usernameExists($username);

		// Checks that user exists
		if ($username != null && $usernameExists){

			// Checks validity of input
			if (strlen($message) > 2500){

				$response["message"] = "Message is too long.";

			}elseif (strlen($keywords) > 256){

				$response["message"] = "Keywords are too long.";

			}elseif (strpos($keywords, ',') !== false){

				$response["message"] = "Separate keywords with spaces, not commas.";

			}else{

				// Start validating keywords, put them in array
				$keywordsArray = array_unique(explode(" ", $keywords));

				// Check that too many (5+) unique keywords don't exist
				if (count($keywordsArray) > 5){
					$response["message"] = "Too many unique keywords.";
				}elseif (!keywordLengthCheck($keywordsArray)){
					$response["message"]
						= "One or more of the keywords were longer than 24 characters.";
				}// Create post and keywords, username is known
				else{

					$postId = DbManager::postMessage($username, $message);

					foreach ($keywordsArray as $keyword){
						DbManager::postKeyword($postId, $keyword);
					}

					$response["status"] = "success";
					$response["message"] = "Message was posted.";
				}
			}
		}else{
			$response["status"] = "authfail";
		}
	}else{
		$response["message"] = "Message field was empty.";
	}
}else{
	$response["status"] = "authfail";
}

// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);