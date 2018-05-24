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
    <title>Reset password</title>
    <script src="js/resetPassword.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
</header>

<main>
    <div>
        <h2>Reset Password</h2>
		<p>Enter your email and reset token aswell as your new password twice. Click the Reset password button to confirm password change.</p>
        <form id="resetPasswordForm">
            <input type="email" placeholder="Email" name="email" id="email-field" required>
			<input type="text" placeholder="Reset token" name="resettoken" id="resettoken-field" required>
            <input type="password" placeholder="New password" name="newpass1" id="newpass1-field" required>
            <input type="password" placeholder="Confirm new password" name="newpass2" id="newpass2-field" required>
            <button type="button" id="reset-password-button">Reset</button>
        </form>
        <p id="fill-all-fields" class="error"></p>
	</div>

    <div id="return-message"></div>
    <a href="index.php">Go back</a>

</main>

<footer>
</footer>

</body>
</html>