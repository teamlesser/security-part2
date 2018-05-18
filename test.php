<?php

require_once("utils/util.php");
/**
 * Created by PhpStorm.
 * User: longb
 * Date: 5/18/2018
 * Time: 1:49 PM
 */
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


