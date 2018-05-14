<?php
/**
 * File: register.php
 * Date: 2018-05-14
 * Desc: Page where a new user can register(create a username and password)
 */

/*******************************************************************************
* HTML section starts here
******************************************************************************/
?>
<!DOCTYPE html>
<html lang="sv-SE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

<header>

</header>

<main>
    <div>
        <h2>Register</h2>
        <form id="loginForm">
            <input type="text" placeholder="Username" id="username-field">
            <input type="password" placeholder="Password" id="password-field">
            <input type="password" placeholder="Re-type password" id="password-again-field">
            <input type="email" placeholder="E-mail" id="email-field">
            <button type="button" id="register-button">Register</button>
        </form>
    </div>

    <div id="return-message"></div>
    <a href="index.php">Go back</a>

</main>

<footer>

</footer>

</body>
</html>

