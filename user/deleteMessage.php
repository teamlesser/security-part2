<?php
/**
 * File: deleteMessage.php
 * Date: 2018-05-19
 * Desc: Processes a message deletion and asks the user if
 * they are sure with a copy of the message being deleted.
 */

session_start();

// Util (autoloader)
include_once "../utils/util.php";

// JWT Helper
require_once "../vendor/jwt_helper.php";

// Check that user is logged in, otherwise kick them out to index
if(!isset($_COOKIE["logged_in"])){
    returnToIndex();
}

// Default messages if auth for message does not pass.
$pageMessage = "Not authorized to delete this post.";
$canDelete = false;
$message = "";
$username = "";

// Checks that request method is POST
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])){
    $postId = htmlspecialchars($_POST["delete"]);

    // Check that post exists and that the current user owns it
    $decodedJWT = JWT::decode($_COOKIE["logged_in"], Config::getInstance()->getSetting("JWTSecretKey"));
    $username = $decodedJWT->username;

    if (DbManager::messageExistsAndIsOwnedByUser($username, $postId)){
        // Display "are you sure" message along with the post being deleted.
        $pageMessage = "Are you sure you want to delete the below message?";
        $canDelete = true;
        $message = DbManager::getMessageById($postId);

        $deleteArray = array("postId"=> $postId, "timeIssued" => date("Y-m-d h:i:s"));

        $_SESSION["deleteAuthValue"] = JWT::encode($deleteArray, Config::getInstance()->getSetting("JWTSecretKey"));
        setcookie("delete_auth", $_SESSION["deleteAuthValue"]);
    }
}

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
?>

<!DOCTYPE html>
<html lang="sv-SE">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete message</title>
        <link rel="stylesheet" href="../css/style.css"/>
        <script src="../js/deleteMessage.js"></script>
    </head>

    <body>

        <header>
            <h1>Delete message</h1>
        </header>

        <main>
            <p><?php echo $pageMessage ?></p>

            <?php if ($canDelete):?>
                <div id="messages">
                    <div class="message">
                        <p><?php echo $message->getMessage() ?></p>
                        <div class="message-info">

                            <p><span class="bold">By:</span> <?php echo $message->getUsername() ?></p>

                            <?php if (!empty($message->getKeywords())): ?>
                                <p class="keyword"><span class="bold">Keywords:</span> <?php foreach($message->getKeywords() as $keyword) {echo $keyword . " ";} ?></p>
                            <?php endif ?>

                            <p><span class="bold">Date:</span> <?php echo $message->getDate() ?></p>
                        </div>
                    </div>
                </div>
                <button id="button-yes" value="<?php echo $_SESSION["deleteAuthValue"]?>">Yes</button><button id="button-no">No</button>
            <?php endif; ?>

            <div id="return-message"></div>
        </main>

        <footer></footer>

    </body>

</html>
