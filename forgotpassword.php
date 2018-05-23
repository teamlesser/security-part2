<?php
/**
 * File: forgotpassword.php
 * Date: 2018-05-19
 * Desc: Page where a new user can request an email for an forgotten password
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
    <title>Forgot password</title>
    <script src="js/forgotPassword.js"></script> 
</head>
<body>

<header>
</header>

<main>
    <div>
        <h2>Forgot Password</h2>
		<p>Enter your e-mail and we will send you a password recovery link.</p>
        <form id="forgottenPasswordForm">
            <label for="email-field">Email:</label><br/>
            <input type="email" placeholder="Enter" name="email" id="email-field" required><br/>
            <button type="button" id="forgot-password-button">Forgot Password</button>
        </form>
	</div>

    <div id="return-message"></div>
    <a href="index.php">Go back</a>

</main>

<footer>
</footer>

</body>
</html>
