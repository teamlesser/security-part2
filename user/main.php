<?php
/**
 * File: main.php
 * Date: 2018-05-17
 * Desc: Page with which the user will interact with posting messages,
 * filtering messages by user or keyword, deleting their own messages and
 * sorting messages by popularity and date.
 */

session_start();

require_once "../utils/util.php";
require_once "../vendor/jwt_helper.php";



// Will contain Message-objects if user is valid
$messages = [];

// Contains user data if valid.
$decodedJWT = "";

// User username if JWT is valid.
$username = "";

// Clears postid for session (one that the user thought about deleting)
unset($_SESSION["deleteAuthValue"]);
setcookie('delete_auth', null, -1, '/securitylab/user/');

// Checks that user login cookie is set and valid before allowing on this page
if (!isset($_COOKIE["logged_in"])){

	returnToIndex();

}else{
	$username = getJWTUsername();
	$messages = DbManager::getAllMessages();
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

    <div id="div-logged-in-as">Logged in as <?php echo $username ?></div>

    <br>

    <textarea id="textarea-message" name="Message" rows="5" cols="40"
              placeholder="Your message..."></textarea>

    <br>

    <label for="input-keywords">Keywords</label><input id="input-keywords"
                                                       placeholder="Keywords...">
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

		<?php foreach ($messages as $message): ?>
            <div class="message">
                <div class="voting">
                    <?php $thisUserVote = DbManager::userHasVoted($username, $message->getMessageId()) ?>

                    <a href="doVote.php?vote=up&id=<?php echo $message->getMessageId(); ?>">

                        <img class="upvote" src="
                        <?php if ($thisUserVote == 1){echo '../img/upvote_selected.png';}
                        else {echo '../img/upvote_unselected.png';} ?>" />

                    </a>

                    <p class="count"><?php echo $message->getVotes(); ?></p>

                    <a href="doVote.php?vote=down&id=<?php echo $message->getMessageId(); ?>">

                        <img class="downvote" src="<?php if ($thisUserVote == -1){echo '../img/downvote_selected.png';}
                        else {echo '../img/downvote_unselected.png';} ?>" />

                    </a>

                </div>

                <div class="message-contents">
                <p><?php echo $message->getMessage() ?></p>

                    <div class="message-info">

                        <p><span class="bold">By:</span> <?php echo $message->getUsername() ?></p>

                        <?php if (!empty($message->getKeywords())): ?>
                            <p class="keyword"><span class="bold">Keywords:</span> <?php
                                foreach ($message->getKeywords() as $keyword){
                                    echo $keyword . " ";
                                } ?></p>
                        <?php endif ?>

                        <p><span class="bold">Date:</span> <?php echo $message->getDate() ?></p>

                        <?php if ($username === $message->getUsername()): ?>

                            <form action="deleteMessage.php" method="post">
                                <button type="submit" value="<?php echo $message->getMessageId(); ?>"
                                        name="delete">Delete</button>
                            </form>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>

    </div>

</main>

<footer>

</footer>
</body>
</html>
