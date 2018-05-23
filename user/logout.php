<?php
/**
 * File: logout.php
 * Date: 2018-05-23
 * Time: 10:21
 */

$responseText = array('message' => "Something went wrong!");

//unset cookie and reset with expired date
if (isset($_COOKIE['logged_in'])) {
    unset($_COOKIE['logged_in']);
    setcookie('logged_in', '', time() - 3600, '/');

    $responseText = array('message' => "You are now logged out. Redirecting to start page!");
}

header('Content-Type: application/json');
echo json_encode($responseText);