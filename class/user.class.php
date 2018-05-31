<?php
/**
 * Created by PhpStorm.
 * User: longb
 * Date: 5/21/2018
 * Time: 2:41 AM
 */

class User{
	private $username;
	private $password;
	private $email;
	private $verified;
	private $id;


	public function __construct($id = null, $username, $password, $email, $verified = false){
		$this->username = $username;
		$this->password = $password;
		$this->email = $email;
		$this->verified = $verified;
		$this->id = $id;

	}

	/**
	 * Create a user object from the database table
	 *
	 * @param array $user row from the user table
	 *
	 * @return null|User null if nothing is returned else an object
	 */
	public static function userFromDb(array $user): User{
		if (!empty($user)){
			return new User($user["id"], $user["username"], $user["password"], $user["email"],
				$user["verified"]);
		}

		return null;
	}

	public function getUsername(): string{
		return $this->username;
	}

	public function getPassword(): string{
		return $this->password;
	}

	public function getEmail(): string{
		return $this->email;
	}

	public function getStatus(){
		return $this->verified;
	}

	public function getId(){
		return $this->id;
	}

	public function getUserPosts(){
		return DbManager::getMessageByUserName($this->username);
	}

	public function getUserDetails(){
		$posts["posts"] = $this->getUserPosts();
		$user["user"] = [
			$this->id,
			$this->username,
			$this->password,
			$this->email,
			$this->verified,
		];

		return array_merge($user, $posts);
	}

	/**
	 * Display the details of a user
	 */
	public function displayUser(){
		$posts = $this->getUserPosts();
		$numOfPosts = count($posts);
		echo "Id = $this->id <br>Username = $this->username<br>Password = $this->password<br>" .
		     "Email = $this->email<br>Verified = $this->verified<br>User posts: $numOfPosts<br>";
		foreach ($posts as $post){
			$post->displayMessage();
		}
		newLine();
	}
}