<?php
/**
 * File: main.php
 * Date: 2018-05-17
 * Desc: Page with which the user will interact with posting messages,
 * filtering messages by user or keyword, deleting their own messages and
 * sorting messages by popularity and date.
 */

require_once "../utils/util.php";
require_once "../vendor/jwt_helper.php";

// TODO: Move this function from this file and processLogin.php to DatabaseManager and use
// TODO: it through a DatabaseManager instance.

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
 * Gets all messages from database.
 * @return Message[] Array of Message-objects.
 */
function getAllMessagesFromDatabase(){

    // Message[]
    $messages = [];

    $query = "SELECT message.id, users.username, message.message, message.date FROM securitylab.message
          INNER JOIN securitylab.users ON users.id = message.user_id
          ORDER BY date DESC;";

    // Gets all posts and usernames for them
    $db = Database::getInstance();
    $result = $db->doSimpleQuery($query);

    while($row = pg_fetch_row($result)){
        $messages[] = new Message($row[0], $row[1], $row[2], $row[3]);
    }

    pg_free_result($result);

    return $messages;
}

/**
 * Returns user to index.php and exits.
 */
function returnToIndex(){
    header('Location: ../index.php');
    exit();
}

/**
 * Overwrites cookie contents and expires it.
 */
function destroyCookie(){
    //TODO: Has to be implemented
}

// Will contain Message-objects if user is valid
$messages = [];

// Checks that user login cookie is set and valid before allowing on this page
if(!isset($_COOKIE["logged_in"])){

    returnToIndex();

} else {
    $jwt = $_COOKIE["logged_in"];
    $decodedJWT;

    try{
        $decodedJWT = JWT::decode($jwt, Config::getInstance()->getSetting("JWTSecretKey"));
    }

    // UnexpectedValueException occurs if signature is wrong.
    catch(UnexpectedValueException $uve){
        echo $uve->getMessage();
        exit();
    }

    catch (DomainException $de){
        echo $de->getMessage();
        exit();
    }

    if (!($decodedJWT != null && usernameExists($decodedJWT->username))){
        returnToIndex();
    }

    $messages = getAllMessagesFromDatabase();
}
?>

<!--**** HTML-SECTION STARTS HERE ****-->

<!DOCTYPE html>
<html lang="sv-SE">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Main site</title>
        <script src="../js/main.js"></script>
        <link rel="stylesheet" href="../css/style.css"/>
    </head>

<body>

<header>

</header>

<main>

    <h1>Messages</h1>

    <div id="div-logged-in-as">Logged in as <?php echo $decodedJWT->username?></div>

    <br>

    <textarea id="textarea-message" name="Message" rows="5" cols="40" placeholder="Your message..."></textarea>

    <br>

    <label for="input-keywords">Keywords</label><input id="input-keywords" placeholder="Keywords...">
    <button id="button-post-message">Post</button>

    <div id="return-message"></div>

    <br>

    <button id="button-sort-popularity">Sort by popularity</button>
    <button id="button-sort-date">Sort by date</button>

    <br><br>

    <input id="input-search-by-user" placeholder="Search by user...">
    <button id="button-search-by-user">Search</button>

    <br><br>

    <input id="input-search-by-keyword" placeholder="Search by keyword...">
    <button id="button-search-by-keyword">Search</button>

    <br>

    <div id="messages">

        <?php foreach ($messages as $message):?>
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
        <?php endforeach; ?>

    </div>

    <!-- A general test on the order of the elements in a message. Needs to be styled in CSS. -->
    <!-- We also need to somehow generate messages in this format, probably with a for-loop in JS. -->
    <!-- One question is how we will safely convey which message should be upvoted, could the user -->
    <!-- Modify the page source and set for example the id of one post to another? -->
    <h4>Dummy message</h4>

    <div class="message">
        <input type="image" src="../img/upvote_unselected.png" alt="Submit" width="48" height="48">
        <p>0</p>
        <input type="image" src="../img/downvote_unselected.png" alt="Submit" width="48" height="48">
        <p>Hello world!</p>
        <p>By: Username</p>
        <p>Keywords: keyword wordkey</p>
        <p>Date: yyyy-mm-dd</p>
    </div>

</main>

<footer>

</footer>
</body>
</html>
