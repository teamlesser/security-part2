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
			$users = self::getAllUsers();

			if (self::isUnique($username, $users)){ // is unique user, proceed
				$query = "INSERT INTO securitylab.users (username, password, email)" .
				         "VALUES ($1, $2, $3)";

				$params = array($username, $password, $email);

				$results = Database::getInstance()->doParamQuery($query, $params);


				if ($results && pg_affected_rows($results) > 0){ // table row has been altered
					freeResource($results);

					return "Registration successful";

				}else{ // no table row altered
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
	 * Check if a value is unique.
	 * This function is intended to serve as generic function to check for uniqueness using an
	 * identifier against a pg_fetch_all results
	 *
	 * @see http://php.net/manual/en/function.in-array.php#106319
	 *
	 * @param string $item      item to search
	 * @param array  $container container containing all items
	 *
	 * @return bool true if unique else false
	 */
	private static function isUnique(string $item, array $container): bool{
		if (!empty($container)){
			foreach ($container as $array){
				if (in_array($item, $array, true)){
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Determine that a username exits in the database
	 *
	 * @param string $username
	 *
	 * @return bool
	 */
	public static function userExists(string $attribute): bool{
		$all = self::getAllUsers();

		return !self::isUnique($attribute, $all);
	}

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
		freeResource($results);

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
		if (!empty($username)){
			$query = "SELECT * FROM securitylab.users u WHERE u.username = $1";
			$params = array($username);

			$results = Database::getInstance()->doParamQuery($query, $params);

			$userDetails = [];

			if ($results != null && pg_affected_rows($results) > 0){
				$userDetails = pg_fetch_assoc($results);
			}
		}
		freeResource($results);

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
		freeResource($results);
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
			freeResource($result);

			// Check the id of the most recent post for this user and return it
			$query = "SELECT id FROM securitylab.message " .
			         "WHERE user_id = (SELECT id FROM securitylab.users u WHERE u.username = $1) " .
			         "GROUP BY id " .
			         "ORDER BY max(date) DESC;";

			$param = array($username);
			$result = $db->doParamQuery($query, $param);

			// Get the topmost entry and return value
			$postId = pg_fetch_result($result, 0, 0);
			freeResource($result);

			return $postId;
		}

		return null;
	}

	/**
	 * Get all messages in the database
	 *
	 * @return array an array with all the messages and their attributes from the database
	 */
	public static function getAllMessages(){

		// Message[]
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
	 * Get a message by attribute
	 *
	 * @param $attribute
	 *
	 * @return array
	 */
	public static function getMessageByKeyword(string $attribute): array{
		$messages = self::getAllMessages();
		$message = [];
		if (!empty($messages)){
			foreach ($messages as $array){
				if (in_array($attribute, $array, true)){
					$message = $array;
					break;
				}
			}
		}

		return $message;
	}

	// Post a keyword

	/**
	 * Posts a keyword to a specific post.
	 *
	 * @param $postId int Id of the post.
	 */
	public static function postKeyword($postId, $keyword): void{
		$query = "INSERT INTO securitylab.keyword (message_id, keyword) VALUES ($1,$2);";
		$param = array($postId, $keyword);
		$results = Database::getInstance()->doParamQuery($query, $param);
		freeResource($results);
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

			freeResource($result);
		}

		return $keywords;
	}


}