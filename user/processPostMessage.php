<?php

// Util (autoloader)
include_once "../utils/util.php";

// JWT Helper
require_once "../vendor/jwt_helper.php";

// Response array, set to negative values at first.
$response = array(
    "status" => "error",
    "message" => "error"
);

/**
 * Checks if a username exists in the database and returns a bool.
 * @param $email string The e-mail to search for.
 * @return bool If the email exists in the database.
 */
function usernameExists($username): bool{
    $query = "SELECT EXISTS (SELECT * FROM securitylab.users WHERE username = $1);";
    $param = array($username);

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
 * Posts a message to the database.
 * @param $username string Username of the poster.
 * @param $message string Message that the user posted.
 * @return int|bool If the message could be posted an int is returned.
 */
function postMessage($username, $message){
    $query = "INSERT INTO securitylab.message (user_id, message) VALUES (
	(SELECT id FROM securitylab.users u WHERE u.username = $1),$2);";

    $param = array($username, $message);

    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);

    if ($result != null && $result != false){
        pg_free_result($result);

        // Check the id of the most recent post for this user and return it
        $query = "SELECT id FROM securitylab.message WHERE user_id =
            (SELECT id FROM securitylab.users u WHERE u.username = $1)
            GROUP BY id
            ORDER BY max(date) DESC;";

        $param = array($username);
        $result = $db->doParamQuery($query, $param);

        // Get the topmost entry and return value
        $postId = pg_fetch_result($result, 0, 0);
        pg_free_result($result);

        return $postId;
    }

    return false;
}

/**
 * Checks that all keywords in an array are shorter than 25 chars.
 * @param $keywords string[] The keywords.
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

/**
 * Posts a keyword to a specific post.
 * @param $postId int Id of the post.
 */
function postKeyword($postId, $keyword):void{
    $query = "INSERT INTO securitylab.keyword (message_id, keyword) VALUES ($1,$2);";
    $param = array($postId, $keyword);
    $db = Database::getInstance();
    $db->doParamQuery($query, $param);
}

/**
 * Returns user to index.php and exits.
 */
function returnToIndex(){
    header('Location: ../index.php');
    exit();
}

// Checks that request method is POST and that user is logged in //
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_COOKIE["logged_in"])){

    // Decodes JSON from POST
    $input = json_decode(file_get_contents('php://input'));

    // Checks that required field isn't empty
    if (!empty($input->message)) {

        // Sanitizes input
        $message = htmlspecialchars($input->message);
        $keywords = htmlspecialchars($input->keywords);

        // Gets JWT to check that user is really logged in
        $jwt = JWT::decode($_COOKIE["logged_in"], Config::getInstance()->getSetting("JWTSecretKey"));
        $username = $jwt->username;
        $usernameExists = usernameExists($username);

        // Checks that user exists
        if ($username != null && $usernameExists){

            // Checks validity of input
            if (strlen($message) > 2500){

                $response["message"] = "Message is too long.";

            } else if (strlen($keywords) > 256) {

                $response["message"] = "Keywords are too long.";

            } else if (strpos($keywords, ',') !== false){

                $response["message"] = "Separate keywords with spaces, not commas.";

            } else {

                // Start validating keywords, put them in array
                $keywordsArray = array_unique(explode(" ", $keywords));

                // Check that too many (5+) unique keywords don't exist
                if (count($keywordsArray) > 5){
                    $response["message"] = "Too many unique keywords.";
                }

                else if (!keywordLengthCheck($keywordsArray)){
                    $response["message"] = "One or more of the keywords were longer than 24 characters.";
                }

                // Create post and keywords, username is known
                else {

                    $postId = postMessage($username, $message);

                    foreach ($keywordsArray as $keyword){
                        postKeyword($postId, $keyword);
                    }

                    $response["status"] = "success";
                    $response["message"] = "Message was posted.";
                }
            }
        }
        else {
            $response["status"] = "authfail";
        }
    }
    else {
        $response["message"] = "Message field was empty.";
    }
} else {
    $response["status"] = "authfail";
}

// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);