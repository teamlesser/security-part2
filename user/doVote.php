<?php

// Util (autoloader)
include_once "../utils/util.php";

// JWT Helper
require_once "../vendor/jwt_helper.php";

// Check that user is logged in, otherwise kick them out to index
if(!isset($_COOKIE["logged_in"])){
    returnToIndex();
}

// Get username from JWT
$username = getJWTUsername();

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if (isset($_GET["vote"]) && isset($_GET["id"])){

        $voteType = htmlspecialchars($_GET["vote"]);
        $postNum = htmlspecialchars($_GET["id"]);

        if ($voteType === "up" || $voteType === "down"){

            if (DbManager::getMessageById($postNum) != null){

                $voteInt = ($voteType === "up" ? 1 : -1);

                if (DbManager::doVote($username, $postNum, $voteInt)){
                    echo "Your vote was counted.";
                } else {
                    echo "Database error.";
                }
            }

            else {
                echo "Invalid vote number";
            }
        }

        else {
            echo "Invalid vote type";
        }


    }
}