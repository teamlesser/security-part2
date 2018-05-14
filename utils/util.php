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
    include "class/" . $classfilename . ".class.php";
}
spl_autoload_register('my_autoloader');

