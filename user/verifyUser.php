<?php

// Util (autoloader)
include_once "../utils/util.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["code"])){

    // Does code exist in verify-table?
    $userId = DbManager::getUserIdForVerificationToken($_GET["code"]);

    if ($userId){

        // Set user to verified and delete verify-table entry
        if (DbManager::verifyUser($userId)){

            try {
                $mailer = new Mailer();
                $mailer->sendRegistrationConfirmationEmail(DbManager::getEmailByUserId($userId));
            }

            catch(\PHPMailer\PHPMailer\Exception $e){
                echo "(Could not send registration confirmation e-mail. But you are still verified!)<br>";
            }

            echo "User was verified. You may now login: <a href='../index.php'>Click here</a>";

        } else {
            echo "An error occurred when verifying.";
        }
    }

    else {
        echo "Invalid token.";
    }
}