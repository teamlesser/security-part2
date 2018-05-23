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

// If user searched for something
$searched = false;

// Clears postid for session (one that the user thought about deleting)
unset($_SESSION["deleteAuthValue"]);
setcookie('delete_auth', null, -1, '/securitylab/user/');

// Checks that user login cookie is set and valid before allowing on this page
if (!isset($_COOKIE["logged_in"])){

	returnToIndex();

}else{
	$username = getJWTUsername();
}

// Counts vote, if one was posted
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST["vote"])){

        // Checks that JWT is valid
        $jwtVote = JWT::decode($_POST["vote"], Config::getInstance()->getSetting("JWTSecretKey"));

        if ($jwtVote->username != null && $jwtVote->time != null && $jwtVote->vote != null && $jwtVote->postid != null){

            // Checks that the vote is made by the logged in user
            if ($jwtVote->username === $username){

                $timeIssued = strtotime($jwtVote->time);
                $timeNow = strtotime(date("Y-m-d H:i:s"));

                $timeDifference = $timeNow - $timeIssued;

                // If time difference is less than 5 minutes, the vote is made.
                if ($timeDifference < 300){

                    $postNum = htmlspecialchars($jwtVote->postid);
                    $voteInt = htmlspecialchars($jwtVote->vote);

                    // Do the vote
                    DbManager::doVote($username, $postNum, $voteInt);

                    // Force reload of the page, otherwise the cached results will be shown.
                    // Also prevents re-submitting of form if refreshing
                    header("location: {$_SERVER['PHP_SELF']}");
                }
            }
        }
    } else if (isset($_POST["keyword_search"])) {

        $keyword = htmlspecialchars($_POST["keyword_search"]);
        $messages = DbManager::getMessagesByKeyword($keyword);
        $searched = true;

    } else if (isset($_POST["user_search"])){

        $usernameSearch = htmlspecialchars($_POST["user_search"]);
        $messages = DbManager::getMessageByUserName($usernameSearch);
        $searched = true;

    } else if (isset($_POST["sort_popularity"])){
        $messages = DbManager::getMessagesSortedByVote();
    } else if (isset($_POST["sort_date"])){
        $messages = DbManager::getMessagesSortedByDate();
    }
} else {

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
    <h1>Messages</h1>
</header>

<main>

    <div id="div-logged-in-as">Logged in as <?php echo $username ?></div>
    <div id="logout-div">
        <button type="button" id="logout-button">Logga ut</button>
        <p id="logout-message"></p>
    </div>
    <br>

    <textarea id="textarea-message" name="Message" rows="5" cols="40"
              placeholder="Your message..."></textarea>

    <br>

    <label for="input-keywords">Keywords</label>
    <input id="input-keywords" placeholder="Keywords...">
    <button id="button-post-message">Post</button>

    <div id="return-message"></div>

    <br>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <button id="button-sort-popularity" name="sort_popularity" type="submit">Sort all by popularity</button>
        <button id="button-sort-date" name="sort_date" type="submit">Sort all by date</button>
    </form>

    <br>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input id="input-search-by-user" placeholder="Search by user..." name="user_search">
        <button id="button-search-by-user" type="submit">Search</button>
    </form>

    <br>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input id="input-search-by-keyword" placeholder="Search by keyword..." name="keyword_search">
        <button id="button-search-by-keyword" type="submit">Search</button>
    </form>

    <br>

    <div id="messages">

        <?php $pageLoadTime = date("Y-m-d H:i:s"); ?>

        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>

                <?php $jwtVoteArray = array("username"=>$username, "postid"=>$message->getMessageId(),
                    "time"=> $pageLoadTime); ?>

                <?php $thisUserVote = DbManager::userHasVoted($username, $message->getMessageId()) ?>

                <div class="message">
                    <div class="voting">

                        <?php $jwtVoteArray["vote"] = 1 ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <button class="upvote<?php if($thisUserVote == 1){echo "d";}?>" type="submit" name="vote"
                                    value="<?php echo JWT::encode($jwtVoteArray, Config::getInstance()->getSetting("JWTSecretKey")) ?>" >
                            </button>
                        </form>

                        <p class="count"><?php echo $message->getVotes(); ?></p>

                        <?php $jwtVoteArray["vote"] = -1 ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <button class="downvote<?php if($thisUserVote == -1){echo "d";}?>" type="submit" name="vote"
                                    value="<?php echo JWT::encode($jwtVoteArray, Config::getInstance()->getSetting("JWTSecretKey")) ?>">
                            </button>
                        </form>

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

            <?php if ($searched): ?>
                <p class="message-result"><a href='main.php'>Show all messages</a></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="message-result">No posts found. <?php if ($searched) {echo "<a href='main.php'>Go back?</a>";}?></p>
        <?php endif; ?>

    </div>

</main>

<footer>

</footer>
</body>
</html>
