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
 * @param $class string Name of the class to be loaded.
 */
function my_autoloader($class) {
    $classfilename = strtolower($class);
    include dirname(__DIR__) . "/class/" . $classfilename . ".class.php";
}
spl_autoload_register('my_autoloader');

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
 * @param $email string The e-mail to search for.
 * @return bool If the email exists in the database.
 */
function usernameExists($username): bool{
    $query = "SELECT EXISTS (SELECT * FROM securitylab.users WHERE username = $1);";
    $param = array($username);

    // Result only returns a boolean
    $db = Database::getInstance();
    $result = $db->doParamQuery($query, $param);

    // "t" is true, "f" is false
    if ($result == null || $result == "f"){
        return false;
    } else {
        return true;
    }
}
