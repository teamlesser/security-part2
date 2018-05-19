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
    "status" => "error",
    "message" => "error"
);

/**
 * Checks if an e-mail exists in the database and returns a bool.
 * @param $email string The e-mail to search for.
 * @return bool If the email exists in the database.
 */
function emailExists($email): bool{
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
 * This function checks whether or not the supplied password
 * matches the one for the user in the database with password_verify().
 * @param $email string User email. Used in select.
 * @param $password string Password that is compared with password_verify() against database hash.
 * @return bool If the password existed/verified.
 */
function passwordVerifies($email, $password): bool{
    // Query for the password hash for the email
    $query = "SELECT password FROM securitylab.users WHERE email = $1;";
    $param = array($email);

    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);
    $passwordHash = pg_fetch_result($result, 0, 0);
    pg_free_result($result);

    // If a password does not exist for the user, simply return false.
    if ($passwordHash == null){
        return false;
    }

    // Returns the result of the hash verification
    return password_verify($password, $passwordHash);
}

/**
 * Queries the username for the user with a certain e-mail.
 * Pre-condition is that the user's e-mail has been verified.
 * @param $email string The user's e-mail.
 * @return string The username of the user.
 */
function getUsernameForEmail($email): string{
    // Query for the username with the e-mail
    $query = "SELECT username FROM securitylab.users WHERE email = $1;";
    $param = array($email);

    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);
    $username = pg_fetch_result($result, 0, 0);
    pg_free_result($result);

    if ($username == null){
        return "";
    }

    return $username;
}


// Checks that request method is POST
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Decodes JSON from POST
    $input = json_decode(file_get_contents('php://input'));

    // Checks that required fields aren't empty
    if (!empty($input->email) && !empty($input->password)) {

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

            // Login succeeded-path
            if ($emailResult && $passwordResult){

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

            // Login failed-path
            else {
                $response["message"] = "Login failed.";
            }
        }

        else {
            $response["message"] = "Wrong format on e-mail.";
        }
    }

    else {
        $response["message"] = "One or more fields were empty.";
    }

}

// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);