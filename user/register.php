<?php
/**
 * File: register.php
 * Date: 2018-05-14
 * Desc: Page where a new user can register(create a username and password)
 */

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
$title = "Security Lab - Group Four";
?>
<!DOCTYPE html>
<html lang="sv-SE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DT167G - <?php echo $title ?></title>
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<header>

</header>

<main>
    <div id="register-div">
        <h1>Register</h1>
        <form id="loginForm">

             <input type="text" placeholder="Username" id="username-field" name="username">
             <input type="password" placeholder="Password" id="password-field" name="password">
             <input type="password" placeholder="Re-type password" id="password-again-field" name="password_again">
             <input type="email" placeholder="E-mail" id="email-field" name="email">
             <button type="button" id="register-button" name="registerButton">Register</button>
        </form>

        <div id="return-message"></div>
        <a href="index.php">Go back</a>
    </div>

<<<<<<< HEAD:user/register.php
    <div id="return-message"></div>
    <a href="../index.php">Go back</a>
=======

>>>>>>> Added style.css to css folder. Changed functionality in register.php so that it passes the style template:register.php

</main>

<footer>

</footer>

</body>
</html>

