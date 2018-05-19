<?php
/**
 * File: resetpassword.php
 * Date: 2018-05-19
 * Desc: Page where a new user can try to reset their password and choose a new one with the help of a token validation system
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
    <script src="js/resetpassword.js"></script> 
</head>
<body>

<header>
</header>

<main>
    <div>
        <h2>Reset Password</h2>
        <form id="resetPasswordForm">
            <label for="email-field">Email:</label><br/>
            <input type="email" placeholder="Enter your email..." name="email" id="email-field" required><br/>
			<label for="resettoken-field">Reset token:</label><br/>
			<input type="text" placeholder="Enter reset token" name="resettoken" id="resettoken-field" required><br/>
			<label for="newpass1-field">New password:</label><br/>
            <input type="password" placeholder="Enter new password" name="newpass1" id="newpass1-field" required><br/>
			<label for="newpass2-field">Confirm new password:</label><br/>
            <input type="password" placeholder="Confirm new password" name="newpass2" id="newpass2-field" required><br/>
            <button type="button" id="reset-password-button">Reset Password</button>
        </form>
	</div>

    <div id="return-message"></div>
    <a href="index.php">Go back</a>

</main>

<footer>
</footer>

</body>
</html>