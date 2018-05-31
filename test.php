<?php

require_once("utils/util.php");
/**
 * Created by PhpStorm.
 * Test database stuff
 * Date: 5/18/2018
 * Time: 1:49 PM
 */


// see all users
echo "<b>All users </b><br>";
$users = DbManager::getAllUsers();

foreach ($users as $user){

	$user->displayUser();
	newLine();
}

// see all messages
echo "<b>All messages </b><br>";
$messages = DbManager::getAllMessages();
foreach ($messages as $message){
	$message->displayMessage();
	newLine();
}

echo "<b>Attempting to get members by attribute </b><br>";
// Get a user by an attribute
$user = DbManager::getUserByAttribute("marvis2000@yahoo.com"); // get by email
$user2 = DbManager::getUserByAttribute("blongho"); // get user by username
$user3 = DbManager::getUserByAttribute(2); // get user by id
$user4
	= DbManager::getUserByAttribute("$2y$10$pL08Muz.3J3lFLBkiikp4OBTkcHIxLqtfuelKs3hQMajB12Cg2DIK");
echo "<b>By email</b><br>";
displayArray($user);
newLine();

echo "<b>By username </b>";
displayArray($user2);

echo "<b>By id </b>";
displayArray($user3);

echo "<b>By password </b>";
displayArray($user4); // fails! php interprets the password as a variable after the second $


echo DbManager::addUser("blongho", "Another user", "blongho@gmail.com");
// fails, blongho already in database
newLine();

echo DbManager::addUser("jondoh", "marvis laje ", "longhoe"); // fails,  bad email
newLine();

echo DbManager::addUser("jacko", "mickaelJacksonIsPopKing", "mj@yahoo.com");
// success if not in table
newLine();


echo "<b>Get details for a particular user</b>";
$username = "marviso";

echo "User details for <b>$username</b>" . newLine();
$userDetails = DbManager::getUser($username);
// To see the details of this user
displayArray($userDetails);

// Get just the password. If validating for login, you can now use this password to check if that given by the user matches
echo "Password for <b>$username</b> is <b>" . $userDetails["password"] . "</b>";


// Get the keywords for message id 2
$keywords = DbManager::getPostKeyword(2);
displayArray($keywords);


