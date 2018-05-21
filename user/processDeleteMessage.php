<?php

session_start();

// Util (autoloader)
include_once "../utils/util.php";

// JWT Helper
require_once "../vendor/jwt_helper.php";

// Check that user is logged in, otherwise kick them out to index
if(!isset($_COOKIE["logged_in"])){
    returnToIndex();
}

// Response array, set to negative values at first.
$response = array(
    "status"  => "error",
    "message" => "Deletion failed.",
);

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if (isset($_SESSION["deleteAuthValue"]) && $_COOKIE["delete_auth"]) {

        // Check that values match
        if ($_SESSION["deleteAuthValue"] === $_COOKIE["delete_auth"]) {

            // Decode value with JWT
            try {
                $jwtPayload = JWT::decode($_SESSION["deleteAuthValue"], Config::getInstance()->getSetting("JWTSecretKey"));

                if ($jwtPayload != null) {

                    // Check time (that <180 seconds has passed since issue)
                    $timeIssued = strtotime($jwtPayload->timeIssued);
                    $timeNow = strtotime(date("Y-m-d H:i:s"));

                    $timeDifference = $timeNow - $timeIssued;

                    if ($timeDifference < 180) {
                        if (DbManager::deletePost($jwtPayload->postId)) {
                            $response["status"] = "success";
                            $response["message"] = "Your message was deleted.";
                        } else {
                            $response["message"] = "An error occurred during deletion.";
                        }
                    } else {
                        $response["message"] = "Deletion request expired, please try again.";
                    }
                }
            } // UnexpectedValueException occurs if signature is wrong.
            catch (UnexpectedValueException $uve) {
                $response["message"] = "Deletion failed, forged value.";
            } catch (DomainException $de) {
                $response["message"] = "Deletion failed, domain.";
            }
        }
    }
}
// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);