<?php
/**
 * File: processRegister.php
 * Date: 2018-05-18
 * Time: 12:41
 */

// Util (autoloader)
include_once "../utils/util.php";

$response = array(
    "status"  => "error",
    "message" => "error",
);

if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $input = json_decode(file_get_contents('php://input'));

    // Checks that required fields aren't empty
    if (!empty($input->username) && !empty($input->password) && !empty($input->passwordAgain) && !empty($input->email)){

        //CHECK THAT LENGTH OF USERNAME IS NOT OVER 64 CHARS
        $userNameLength = strlen(utf8_decode($input->username));
        if($userNameLength <= 64){
            //replace '%40' with '@'
            $email = urldecode($input->email);

            // Remove all illegal characters from email
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            //make sure email-address is in a valid format
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $psw = $input->password;
                $pswAgain = $input->passwordAgain;

            //make sure that password and confirmational password match AND ARE BETWEEN 8-64 CHARS LONG
            if(strcmp($psw, $pswAgain) == 0 && strlen(utf8_decode($psw)) >= 8 && strlen(utf8_decode($psw)) <= 64){

                    //sanitize username
                    $userName = filter_var($input->username, FILTER_SANITIZE_STRING);
                    //hash password
                    $psw = password_hash($psw, PASSWORD_DEFAULT);

                    //try to add user to database
                    $response["message"] = DbManager::addUser($userName, $psw, $email);
                    $response["status"] = "success";

                }
                else{
                    $response["message"] = "Password and confirmation password don't match OR password is not between 8-64 chars!";
                }
            }
            else {
                $response["message"] = "Email is not valid!";
            }
        }
        else{
            $response["message"] = "Username is longer than maximum 64 chars!";
        }
    }else{
        $response["message"] = "One or more fields were empty.";
    }
}

// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);

?>