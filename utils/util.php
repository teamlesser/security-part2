<?php
/*******************************************************************************
 * File: util.php
 *
 * Desc: Contains class autoloader. May contain more commonly used functions
 * that are not tied to any particular object.
 *
 * Date: 2018-05-08
 ******************************************************************************/

/**
 * Autoloader for class files. Class filename must
 * be lower case and present in class-directory.
 * Filename format for classes: classname.class.php
 *
 * @param $class string Name of the class to be loaded.
 */

function my_autoloader($class){
	$classfilename = strtolower($class);
	include dirname(__DIR__) . "/class/" . $classfilename . ".class.php";

}

spl_autoload_register('my_autoloader');

// UTILITY FUNCTIONS
/**
 * @see  http://php.net/manual/en/function.pg-escape-literal.php
 *
 * @param string $string
 *
 * @return string
 */
function escapeString(string $string): string{
	return pg_escape_literal($string);
}

// Test functions

/**
 * Display an array using print_r formatted
 *
 * @param $arr
 */
function displayArrayUnformatted($arr){
	echo "<br><pre>";
	print_r($arr);
	echo "</pre><br>";
}


/**
 * Display the key-value pairs of an array
 *
 * @param $arr
 */
function displayArray($arr){
	if (empty($arr) || is_null($arr)){
		echo "<br>Nothing to display<br>";
	}else{
		echo "<br>";
		foreach ($arr as $key => $value){
			echo "$key => $value<br>";
		}
		echo "<br>";
	}
}

function isValidEmail(string $email): bool{
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Display array of arrays
 *
 * @param $arrayOfArrays
 */
function displayNestedArray($arrayOfArrays){
	if (empty($arrayOfArrays) || is_null($arrayOfArrays)){
		echo "<br>Nothing to display<br>";
	}else{
		foreach ($arrayOfArrays as $array){
			displayArray($array);
		}
	}
}

function newLine(){
	echo "<br>";
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

/**
 * Checks if a username exists in the database and returns a bool.
 *
 * @param $email string The e-mail to search for.
 *
 * @return bool If the email exists in the database.
 */
function usernameExists($username): bool{
    return !empty(DbManager::getUserByAttribute($username));
}



/**
 * Gets username from JWT cookie.
 * @return string The logged_in users username.
 */

function getJWTUsername(){
    $jwt = $_COOKIE["logged_in"];
    $username = "";

    try{
        $decodedJWT = JWT::decode($jwt, Config::getInstance()->getSetting("JWTSecretKey"));
        $username = $decodedJWT->username;
    }

        // UnexpectedValueException occurs if signature is wrong.
    catch (UnexpectedValueException $uve){
        echo $uve->getMessage();
        exit();
    }

    catch (DomainException $de){
        echo $de->getMessage();
        exit();
    }

    if (!($decodedJWT != null && ($decodedJWT->username))){
        returnToIndex();
    }

    return $username;
}
