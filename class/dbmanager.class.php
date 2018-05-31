<?php

/*******************************************************************************
 * File: database.class.php
 *
 * Desc: Handles communication with the database to connect, and update tables
 * Uses: Database.class.php
 * This class is a proxy to the Database connection. Each function is only
 * called needed.
 *
 * NB: This class only concerns itself with communications with the database. Before calling any
 * function herein, you are responsible for the sanity of the data e.g that a user entered a
 * valid email or a phonenumber is in the valid format.
 *
 * However, a minor check (that a value is not empty) is done before attempting any connection
 *
 * Date: 2018-05-18
 ******************************************************************************/
class DbManager{

	/**
	 * DbManager constructor is empty. Let the heavy lifting be done by the respective functions
	 */
	public function __construct(){
	}

    // *****************************************************************************
    // REGISTER
    // *****************************************************************************

    /**
     * Add a user to the database.
     *
     * @param string $username User's username
     * @param string $password User's password
     * @param string $email    User's email
     *
     * If any of the params is empty, this function returns "Registration refused!"
     *
     * @return string Registration successful/refused
     */
    public static function addUser(string $username, string $password, string $email): string{
        // if any of username, password and email is empty, do no proceed
        if (!empty($username) && !empty($password) && isValidEmail($email)){

            // Are the username and email unique?
            if (self::isUniqueUsername($username)){

                if (self::isUniqueEmail($email)){
                    $query = "INSERT INTO securitylab.users (username, password, email)" .
                        "VALUES ($1, $2, $3)";

                    $params = array($username, $password, $email);
                    $results = Database::getInstance()->doParamQuery($query, $params);

                    if ($results && pg_affected_rows($results) > 0){ // table row has been altered
                        pg_free_result($results);

                        $token = self::createVerificationToken();

                        // Insert token into table
                        if ($token && self::insertVerificationTokenForUser($username, $token)){

                            // Send registration e-mail with verification token
                            try {
                                $mailer = new Mailer();
                                $mailer->sendRegistrationEmail($email, $token);
                            }
                            catch(PHPMailer\PHPMailer\Exception $e){
                                return "Could not send verification e-mail.";
                            }

                            return "Registration successful";
                        }

                        return "Could not create verification token. Contact admin.";
                    }

                }else{ // email is not unique
                    return "Registration refused!";

                }
            }else{// user is already in the database
                return "Registration refused!";

            }
        }else{// a field is empty
            return "Registration refused!";
        }
    }

    /**
     * Check if a username is unique.
     *
     * @param $username string Username of the user to check existence of
     * @return bool true if unique (returned value is empty) else false
     */
    private static function isUniqueUsername(string $username): bool{
        $query = "SELECT EXISTS(SELECT * FROM securitylab.users WHERE username = $1)";
        $param = array($username);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){
            $row = pg_fetch_row($result);
            pg_free_result($result);

            $bool = $row[0];

            return $bool == "f";
        }

        return false;
    }

    /**
     * Is the email unique?
     * @param string $email User e-mail.
     * @return bool Is the e-mail supplied unique? (Does not exist in database.)
     */
    private static function isUniqueEmail(string $email): bool{
        $query = "SELECT EXISTS(SELECT * FROM securitylab.users WHERE email = $1)";
        $param = array($email);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){
            $row = pg_fetch_row($result);
            pg_free_result($result);

            $bool = $row[0];

            return $bool == "f";
        }

        return false;
    }

    // *****************************************************************************
    // VERIFY
    // *****************************************************************************

    /**
     * Inserts a verification token for a user in a table.
     * @param $username string username of the user
     * @param $token string The token to be entered
     * @return bool If the value could be inserted.
     */
    private static function insertVerificationTokenForUser($username, $token){
        $query = "INSERT INTO securitylab.verify (user_id, verify_token)
            VALUES ((SELECT id FROM securitylab.users WHERE username = $1), $2);";

        $params = array($username, $token);
        $result = Database::getInstance()->doParamQuery($query, $params);

        if (pg_affected_rows($result) == 1){
            pg_free_result($result);
            return true;
        }

        return false;
    }

    /**
     * Generates a verification token.
     * @return bool|string If everything went fine, a verification token, otherwise false.
     */
    private static function createVerificationToken(){

        try{
            $verificationToken = bin2hex(random_bytes(32));
            return $verificationToken;
        }
        catch(Exception $e){
            return false;
        }
    }


    /**
     * Returns the user id for a verification token if it exist. If not, false.
     * @param $token string Token to check for.
     * @return int|bool User id or false.
     */
    public static function getUserIdForVerificationToken($token){
        $query = "SELECT user_id FROM securitylab.verify WHERE verify_token = $1;";
        $param = array($token);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){
            $userId = pg_fetch_row($result)[0];
            pg_free_result($result);

            return $userId;
        }

        return false;
    }

    /**
     * Verifies an user by setting boolean to true in users-table
     * and removing verify-entry.
     * @param $userId int Id of user.
     * @return bool If the user could be verified.
     */
    public static function verifyUser($userId){
        $query = "UPDATE securitylab.users SET verified = TRUE WHERE id = $1";
        $param = array($userId);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if (pg_affected_rows($result) == 1){

            pg_free_result($result);

            // Remove verify entry in verify-table
            $query = "DELETE FROM securitylab.verify WHERE user_id = $1;";
            $result = Database::getInstance()->doParamQuery($query, $param);

            // Entry was deleted
            if (pg_affected_rows($result) == 1){
                pg_free_result($result);
                return true;
            }
        }

        return false;
    }


    /**
     * Checks if a user is verified. Used when logging in.
     * @param $email string Email of the user.
     * @return bool Whether or not they are verified/exist.
     */
    public static function isUserVerified($email){
        $query = "SELECT EXISTS (SELECT * FROM securitylab.users WHERE email = $1 AND verified = true);";
        $param = array($email);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){
            $row = pg_fetch_row($result);
            pg_free_result($result);

            $bool = $row[0];

            return $bool == "t";
        }

        return false;
    }


    // *****************************************************************************
    // USERS
    // *****************************************************************************

    /**
     * Fetch all users in the database
     *
     * @return array All the users in the database and their details
     */
    public static function getAllUsers(): array{
        $query = "SELECT * FROM securitylab.users;";
        $results = Database::getInstance()->doSimpleQuery($query);

        $users = [];
        if (!is_null($results) && pg_affected_rows($results) > 0){
            while ($user = pg_fetch_row($results)){
                $users [] = new User($user[0], $user[1], $user[2], $user[3], $user[4]);
            }
        }
        pg_free_result($results);

        return $users;
    }

    /**
     * Get the details of a particular user from the users table
     *
     * @param string $username The username of the user of interest
     *
     * @return array an associative array with the user's details or null if no user exists
     */
    public static function getUser(string $username): array{
        $userDetails = [];

        if (!empty($username)){
            $query = "SELECT * FROM securitylab.users u WHERE u.username = $1";
            $params = array($username);

            $results = Database::getInstance()->doParamQuery($query, $params);

            if ($results != null && pg_affected_rows($results) > 0){
                $userDetails = pg_fetch_assoc($results);
                pg_free_result($results);
            }
        }

        return $userDetails;
    }

    /**
     * Get a user by an attribute from the securitylab.users table
     *
     * @param string $attribute username|email|password|id|status
     *
     * @return array returns an array with the user's details [id, username, password, email,
     * status]
     */
    public static function getUserByAttribute(string $attribute): array{
        $query = "SELECT * FROM securitylab.users;";
        $results = Database::getInstance()->doSimpleQuery($query);
        $container = [];
        if (!is_null($results) && pg_affected_rows($results) > 0){
            $container = pg_fetch_all($results);
        }
        pg_free_result($results);
        $detailsFromAttribute = [];

        if (!empty($container)){
            foreach ($container as $array){
                if (in_array($attribute, $array, true)){
                    $detailsFromAttribute = $array;
                    break;
                }
            }
        }


        return $detailsFromAttribute;
    }

    /**
     * Checks if an e-mail exists in the database and returns a bool.
     * @param $email string The e-mail to search for.
     * @return bool If the email exists in the database.
     */
    public static function emailExist($email): bool {

        $query = "SELECT EXISTS (SELECT * FROM securitylab.users WHERE email = $1);";
        $param = array($email);

        // Result only returns a boolean
        $result = Database::getInstance()->doParamQuery($query, $param);

        // "t" is true, "f" is false
        if ($result == null || $result == "f"){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Gets user id for e-mail.
     * @param $id string The user email
     * @return string|bool User e-mail or bool
     */
    public static function getEmailByUserId($id){
        $query = "SELECT email FROM securitylab.users WHERE id = $1;";
        $param = array($id);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){
            $row = pg_fetch_assoc($result);
            pg_free_result($result);

            return $row["email"];
        }

        return false;
    }

    // =================================================================
    //          UPDATE MESSAGE TABLE
    // =================================================================
    /**
     * Posts a message to the database.
     *
     * @param $username string Username of the poster.
     * @param $message  string Message that the user posted.
     *
     * @return int|null If the message could be posted an int is returned.
     */
    public static function postMessage(string $username, string $message){
        $query = "INSERT INTO securitylab.message (user_id, message) " .
            "VALUES ((SELECT id FROM securitylab.users u WHERE u.username = $1),$2);";

        $param = array($username, $message);

        $db = Database::getInstance();
        $result = $db->doParamQuery($query, $param);

        if ($result && pg_affected_rows($result) > 0){
            pg_free_result($result);

            // Check the id of the most recent post for this user and return it
            $query = "SELECT id FROM securitylab.message " .
                "WHERE user_id = (SELECT id FROM securitylab.users u WHERE u.username = $1) " .
                "GROUP BY id " .
                "ORDER BY max(date) DESC;";

            $param = array($username);
            $result = $db->doParamQuery($query, $param);

            // Get the topmost entry and return value
            $postId = pg_fetch_result($result, 0, 0);
            pg_free_result($result);

            return $postId;
        }

        return null;
    }

    // *****************************************************************************
    // GETTING MESSAGES
    // *****************************************************************************

    /**
     * Get all messages in the database
     *
     * @return array an array with all the messages and their attributes from the database
     */
    public static function getAllMessages(){
        $messages = [];

        $query
            = "SELECT message.id, users.username, message.message, message.date FROM securitylab.message
          INNER JOIN securitylab.users ON users.id = message.user_id
          ORDER BY date DESC;";

        // Gets all posts and usernames for them
        $db = Database::getInstance();
        $result = $db->doSimpleQuery($query);

        while ($row = pg_fetch_row($result)){
            $messages[] = new Message($row[0], $row[1], $row[2], $row[3]);
        }

        pg_free_result($result);

        return $messages;
    }


    /**
     * Gets a message by id.
     *
     * @param $postId int Id of the message.
     *
     * @return Message|null A message object.
     */
    public static function getMessageById($postId){

        $query
            = "SELECT message.id, users.username, message.message, message.date FROM securitylab.message
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


    /**
	 * @param string $username
	 *
	 * @return array all messsages by the user
	 */
	public static function getMessageByUserName(string $username): array{
		$messages = self::getAllMessages();
		$msg = [];
		foreach ($messages as $message){
			if ($message->getUsername() === $username){
				$msg[] = $message;
			}
		}

		return $msg;
	}

    /**
     * Gets messages by keyword.
     * @param string $keyword The keyword of the post.
     * @return array An array of messages.
     */
	public static function getMessagesByKeyword(string $keyword){
        $query = "SELECT message.id, users.username, message.message, message.date
            FROM securitylab.message 
            INNER JOIN securitylab.users ON users.id = message.user_id
            WHERE message.id IN (SELECT message_id FROM securitylab.keyword WHERE keyword = $1)
            ORDER BY date DESC;";
        $param = array($keyword);

        $db = Database::getInstance();
        $result = $db->doParamQuery($query, $param);

        // Message[]
        $messages = [];

        while ($row = pg_fetch_row($result)){
            $messages[] = new Message($row[0], $row[1], $row[2], $row[3]);
        }

        pg_free_result($result);

        return $messages;
    }


	/**
	 * Get the number of votes that a message has
	 *
	 * @param $messageId
	 *
	 * @return int The number of votes or the message or -999 if wrong message id
	 */
	public static function getMessageVotes($messageId){
		$query = "SELECT sum(vote) FROM securitylab.vote v " .
		         "WHERE v.message_id = $1;";

		$params = array($messageId);

		$count = - 999;
		$results = Database::getInstance()->doParamQuery($query, $params);
		if ($results && pg_affected_rows($results) > 0){
			$count = intval(pg_fetch_result($results, 0, 'sum'));
			pg_free_result($results);
		}

		return $count;

	}

	/**
	 * Get messages sorted by an attribute date|popularity
	 *
	 * @return array
	 */
	public static function getMessagesSortedByDate(): array{

		$messages = self::getAllMessages();

		usort($messages, function (Message $lhs, Message $rhs){
			return $lhs->getDate() < $rhs->getDate();
		});

		return $messages;
	}

	/**
	 * Get messages sorted by popularity (number of positive votes)
	 *
	 * @return array all messages sorted by number of vote
	 */
	public static function getMessagesSortedByVote(): array{
		$messages = self::getAllMessages();

		// Desc sort
		usort($messages, function (Message $lhs, Message $rhs){
			return $lhs->getVotes() < $rhs->getVotes();
		});

		return $messages;
	}

    // *****************************************************************************
    // DELETE MESSAGE
    // *****************************************************************************

    /**
     * Deletes a post from the database by id.
     *
     * @param $postId int Id of the post
     *
     * @return bool If the post could be deleted.
     */
    public static function deletePost($postId){
        $query = "DELETE FROM securitylab.message WHERE message.id = $1;";

        $param = array($postId);
        $db = Database::getInstance();
        $result = $db->doParamQuery($query, $param);

        $countDeleted = pg_affected_rows($result);
        pg_free_result($result);

        if ($countDeleted == 1){
            return true;
        }

        return false;
    }

    /**
     * Checks that the message exists and is owned by the user.
     *
     * @param $username string Username of the user.
     * @param $postId   int Post id.
     *
     * @return bool If the post is owned by the user and both exist.
     */
    public static function messageExistsAndIsOwnedByUser($username, $postId): bool{
        $query
            = "SELECT EXISTS (
        SELECT * FROM securitylab.users 
        INNER JOIN securitylab.message ON message.user_id = users.id 
        WHERE username = $1 AND message.id = $2);";

        $param = array($username, $postId);

        // Result only returns a boolean
        $db = Database::getInstance();
        $result = $db->doParamQuery($query, $param);
        $bool = pg_fetch_result($result, 0, 0);
        pg_free_result($result);

        // "t" is true, "f" is false
        if ($bool == null || $bool == "f"){
            return false;
        }else{
            return true;
        }
    }

    // *****************************************************************************
    // KEYWORDS
    // *****************************************************************************

    /**
     * Posts a keyword to a specific post.
     *
     * @param $postId int Id of the post.
     */
    public static function postKeyword($postId, $keyword): void{
        $query = "INSERT INTO securitylab.keyword (message_id, keyword) VALUES ($1,$2);";
        $param = array($postId, $keyword);
        $results = Database::getInstance()->doParamQuery($query, $param);
        pg_free_result($results);
    }


    /**
     * Get all the keywords for a particular post
     *
     * @param int $msgId The id of the message
     *
     * @return array array containing the keywords
     */
    public static function getPostKeyword(int $msgId){
        // Array of strings
        $keywords = [];

        $query = "SELECT keyword.keyword FROM securitylab.keyword WHERE message_id = $1;";
        $param = array($msgId);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result && pg_affected_rows($result) > 0){

            while ($row = pg_fetch_row($result)){
                $keywords[] = $row[0];
            }

            pg_free_result($result);
        }

        return $keywords;
    }

    // *****************************************************************************
    // VOTING
    // *****************************************************************************

    /**
     * Checks if user has voted on a post.
     * @param $username string Username
     * @param $postId int Message id
     * @return bool
     */
    public static function userHasVoted($username, $postId) {
        $query = "SELECT vote.vote FROM securitylab.vote 
              WHERE user_id = (SELECT id FROM securitylab.users WHERE username = $1) AND message_id = $2;";
        $param = array($username, $postId);

        $db = Database::getInstance();
        $result = $db->doParamQuery($query, $param);

        if ($result == null){
            return false;
        } else {
            // Return what the user had voted previously
            $vote = pg_fetch_array($result);
            pg_free_result($result);
            return $vote[0];
        }
    }

    /**
     * Does a vote by comparing if the user has voted first.
     * The row is either deleted, inserted or updated.
     * @param $username string Username of voter.
     * @param $postId int Message id
     * @param $vote int The vote value (1 = up, -1 = down)
     * @return bool If the vote could be executed.
     */
    public static function doVote($username, $postId, $vote){
        $voteResult = self::userHasVoted($username, $postId);

        $query = "";
        $param = array();

        if ($vote == 1 || $vote == -1){
            if ($voteResult == false){
                // Create new entry
                $query = "INSERT INTO securitylab.vote (user_id, message_id, vote)
                VALUES ((SELECT id FROM securitylab.users WHERE username = $1), $2, $3);";
                $param = array($username, $postId, $vote);

            } else if ($vote == $voteResult) {
                // Delete entry
                $query = "DELETE FROM securitylab.vote WHERE user_id =
                (SELECT id FROM securitylab.users WHERE username = $1) AND message_id = $2;";
                $param = array($username, $postId);

            } else {
                // alter entry
                $query = "UPDATE securitylab.vote 
                SET vote = $1
                WHERE user_id =(SELECT id FROM securitylab.users WHERE username = $2) 
                AND message_id = $3;";

                $param = array($vote, $username, $postId);
            }

            $db = Database::getInstance();
            $result = $db->doParamQuery($query, $param);

            if ($result != false){
                pg_free_result($result);
                return true;
            }
        }

        return false;
    }

    // *****************************************************************************
    // RESET PASSWORD
    // *****************************************************************************

    /**
     * Makes an attempt to add a reset token to the users table
     * @var $email string The email to check in the users table
     * @var $resetToken string The reset token
     * @returns boolean True if the reset token was added successfully, otherwise false
     */
    public static function addResetToken($email, $resetToken) : bool {

        $query = "INSERT INTO securitylab.reset(user_id, reset_token) 
              VALUES ((SELECT id FROM securitylab.users WHERE email = $1), $2);";
        $param = array($email, $resetToken);

        // Result only returns a boolean
        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){
            $affectedRows = pg_affected_rows($result);
            pg_free_result($result);

            return $affectedRows == 1;
        }

        return false;
    }

    /**
     * Check if the user already have a reset token in his/her data
     * @var $email string The email to check in the reset table
     * @returns boolean True if the reset token exists, otherwise false
     */
    public static function resetTokenExist($email) : bool {

        $query = "SELECT EXISTS(SELECT * FROM securitylab.reset WHERE user_id = (SELECT id FROM securitylab.users WHERE email = $1));";
        $param = array($email);

        // Result only returns a boolean
        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result){

            $resArray = pg_fetch_array($result);
            pg_free_result($result);

            return $resArray[0] == "t";
        }

        return false;
    }

    /**
     * Deletes a reset token from a reset table
     *
     * @var $email string The email to check in the users table
     *
     */
    public static function deleteResetToken($email): void{

        $query = "DELETE FROM securitylab.reset WHERE user_id = (SELECT id FROM securitylab.users WHERE email = $1)";
        $param = array($email);

        // Result does not really need to return anything
        Database::getInstance()->doParamQuery($query, $param);
    }


    /**
     * Check if the email entered matches with the users reset token
     *
     * @var $email      string The email to check in the users table
     * @var $resetToken string The reset token
     * @return bool If the combination exists.
     */
    public static function resetTokenEmailMatch($email, $resetToken){

        $query = "SELECT EXISTS(SELECT * FROM securitylab.reset
            WHERE user_id = (SELECT id FROM securitylab.users WHERE email = $1)
            AND reset_token = $2)";

        $param = array($email, $resetToken);
        $result = Database::getInstance()->doParamQuery($query, $param);

        // Check if there was a match
        if ($result){
            $row = pg_fetch_array($result);
            pg_free_result($result);

            return $row[0] == "t";
        }

        return false;
    }

    /**
     * Makes an attempt to change a users password
     *
     * @var $email       string The email to check in the users table
     * @var $newPassword string The new password to change to
     * @returns boolean True if password was changed, otherwise false
     */
    public static function changePassword($email, $newPassword): bool{

        $query = "UPDATE securitylab.users SET password = $1 WHERE email = $2";

        // Password has to be hashed before insertion!
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $param = array($newPassword, $email);

        $result = Database::getInstance()->doParamQuery($query, $param);

        if ($result == null || $result == false){
            return false;
        } else {
            pg_free_result($result);
            return true;
        }
    }
}