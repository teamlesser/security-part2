<?php

// Util (autoloader)
include_once "../utils/util.php";

// JWT Helper
require_once "../vendor/jwt_helper.php";

// Check that user is logged in, otherwise kick them out to index
if (!isset($_COOKIE["logged_in"])){
	returnToIndex();
}else{

// Get username from JWT
	$username = getJWTUsername();

	$response = [];

	if ($_SERVER["REQUEST_METHOD"] == "GET"){

		if (isset($_GET["vote"]) && isset($_GET["id"])){

			$voteType = htmlspecialchars($_GET["vote"]);
			$postNum = htmlspecialchars($_GET["id"]);

			if ($voteType === "up" || $voteType === "down"){

				if (DbManager::getMessageById($postNum) != null){

					$voteInt = ($voteType === "up" ? 1 : - 1);

					if (DbManager::doVote($username, $postNum, $voteInt)){
						$response["status"] = "success";
						$response["message"] = "Your vote was counted";
						//echo "Your vote was counted.";
					}else{
						$response["status"] = "fail";
						$response["message"] = "Error getting your vote";
						//echo "Database error.";
					}
				}else{
					$response["status"] = "fail";
					$response["message"] = "Invalid vote number";
					//echo "Invalid vote number";
				}
			}else{
				$response["status"] = "fail";
				$response["message"] = "Invalid vote type";
				//echo "Invalid vote type";
			}


		}
	}
	echo "<!DOCTYPE html><html><head><title>Voting response</title><script>" .
	     "window.setTimeout(function() {
    window.location.href = 'main.php';}, 2000)</script></head><body>" .
	     "<p>" . $response['message'] . "</p></body></html>";
}
