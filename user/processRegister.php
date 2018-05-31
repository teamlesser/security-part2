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

        //replace '%40' with '@'
        $email = urldecode($input->email);

        // Remove all illegal characters from email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        //make sure email-address is in a valid format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $psw = $input->password;
            $pswAgain = $input->passwordAgain;

            //make sure that password and confirmational password match
            if(strcmp($psw, $pswAgain) == 0){

                //sanitize username
                $userName = filter_var($input->username, FILTER_SANITIZE_STRING);
                //hash password
                $psw = password_hash($psw, PASSWORD_DEFAULT);

                //try to add user to database
                $response["message"] = DbManager::addUser($userName, $psw, $email);
                $response["status"] = "success";

            }
            else{
                $response["message"] = "Password and confirmation password don't match!";
            }
        }
        else {
            $response["message"] = "Email is not valid!";
        }

    }else{
        $response["message"] = "One or more fields were empty.";
    }

}

// Sends response as JSON
header('Content-Type: application/json');
echo json_encode($response);

?>