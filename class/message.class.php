<?php

/*******************************************************************************
 * File: message.class.php
 *
 * Desc: A Message object that handles data about a message, such as who
 * posted it or what keywords it contains, if any.
 *
 * Date: 2018-05-19
 ******************************************************************************/
class Message{

	private $messageId;     // Message id
	private $username;      // Poster username
	private $message;       // The message itself
	private $date;          // The date and time for the post
	private $keywords;      // string[] for keywords for the post
	private $votes;         // number of votes for this message

	/**
	 * Message constructor. Sets data members and gets keywords for post.
	 *
	 * @param $id   int The id of the post.
	 * @param $user string Username of the postee.
	 * @param $msg  string The message.
	 * @param $dt   string Date of the message.
	 */
	public function __construct($id, $user, $msg, $dt){
		// Sets data members
		$this->messageId = $id;
		$this->username = $user;
		$this->message = $msg;

		// Formats date to Y-m-d
		$this->date = new DateTime($dt);
		$this->date = $this->date->format("Y-m-d (H:i:s)");

		// Asks for keywords for post
		$this->keywords = $this->getKeywordsForPost();
	}

	/**
	 * Gets the keywords for this post from the database.
	 *
	 * @return string[] Keywords for the post, if any.
	 */
	private function getKeywordsForPost(){
		return DbManager::getPostKeyword($this->messageId);
	}

	/**
	 * @return int
	 */
	public function getMessageId(): int{
		return $this->messageId;
	}

	/**
	 * @return string
	 */
	public function getDate(): string{
		return $this->date;
	}

	/**
	 * @return string[]
	 */
	public function getKeywords(): array{
		return $this->keywords;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string{
		return $this->username;
	}

	/**
	 * Get the number of votes that this message has had so far.
	 *
	 * @return int number of votes or -999 if wrong message id
	 */
	public function getVoteSum(): int{
		return DbManager::getMessageVotes($this->messageId);
	}

	/**
	 * Display a message in details
	 */
	public function displayMessage(){
		echo "Message id = $this->messageId<br>User = $this->username<br>Message = $this->message<br>" .
		     "Date and time = $this->date<br>Keywords: ";
		foreach ($this->keywords as $keyword){
			echo "$keyword ";
		}
		echo "<br>Vote sum: " . $this->getVoteSum() . "<br>";

	}

}