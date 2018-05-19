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

/**
 * Checks that the message exists and is owned by the user.
 * @param $username string Username of the user.
 * @param $postId int Post id.
 * @return bool If the post is owned by the user and both exist.
 */
function messageExistsAndIsOwnedByUser($username, $postId):bool {
    $query = "SELECT EXISTS (
        SELECT * FROM securitylab.users 
        INNER JOIN securitylab.message ON message.user_id = users.id 
        WHERE username = $1 AND message.id = $2);";

    $param = array($username, $postId);

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
 * Gets a message by id.
 * @param $postId int Id of the message.
 * @return Message|null A message object.
 */
function getMessageById($postId) {

    $query = "SELECT message.id, users.username, message.message, message.date FROM securitylab.message
        INNER JOIN securitylab.users ON users.id = message.user_id
        WHERE message.id = $1;";

    $param = array($postId);

    // Result only returns a boolean
    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);

    if ($result){
        $msgData = pg_fetch_row($result, 0);
        pg_free_result($result);
        $message = new Message($msgData[0], $msgData[1], $msgData[2], $msgData[3]);
        return $message;
    }

    return null;
}


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
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $postId = htmlspecialchars($_POST["delete"]);

    // Check that post exists and that the current user owns it
    $decodedJWT = JWT::decode($_COOKIE["logged_in"], Config::getInstance()->getSetting("JWTSecretKey"));
    $username = $decodedJWT->username;

    if (messageExistsAndIsOwnedByUser($username, $postId)){
        // Display "are you sure" message along with the post being deleted.
        $pageMessage = "Are you sure you want to delete the below message?";
        $canDelete = true;
        $message = getMessageById($postId);
        $_SESSION["postid"] = $postId;
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
                <button id="button-yes">Yes</button><button id="button-no">No</button>
            <?php endif; ?>

            <div id="return-message"></div>
        </main>

        <footer></footer>

    </body>

</html>
