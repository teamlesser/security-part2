<?php

require_once("utils/util.php");
/**
 * Created by PhpStorm.
 * User: longb
 * Date: 5/18/2018
 * Time: 1:49 PM
 */

// Get a user by an attribute
$user = DbManager::getUserByAttribute("marvis2000@yahoo.com"); // get by email
$user2 = DbManager::getUserByAttribute("blongho"); // get user by username
$user3 = DbManager::getUserByAttribute(2); // get user by id
$user4
	= DbManager::getUserByAttribute("$2y$10$pL08Muz.3J3lFLBkiikp4OBTkcHIxLqtfuelKs3hQMajB12Cg2DIK");
echo "By email<br>";
displayArray($user);
newLine();

echo "By username <br>";
displayArray($user2);

echo "By id <br>";
displayArray($user3);

echo "By password <br>";
displayArray($user4); // fails! php interprets the password as a variable after the second $


echo DbManager::addUser("blongho", "Another user", "blongho@gmail.com");
// fails, blongho already in database
newLine();

echo DbManager::addUser("jondoh", "marvis laje ", "longhoe"); // fails,  bad email
newLine();

echo DbManager::addUser("jacko", "mickaelJacksonIsPopKing", "mj@yahoo.com");
// success if not in table
newLine();

//See current users
displayNestedArray(DbManager::getAllUsers());


// get details for a particular user
$username = "marviso";

echo "User details for <b>$username</b>" . newLine();
$userDetails = DbManager::getUser($username);
// To see the details of this user
displayArray($userDetails);

// Get just the password. If validating for login, you can now use this password to check if that given by the user matches
echo "Password for <b>$username</b> is <b>" . $userDetails["password"] . "</b>";


