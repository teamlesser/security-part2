<?php
/**
 * File: processForgotPassword.php
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
    <script src="../js/forgotpassword.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
</header>

<main>
    <div>
        <h2>Forgot Password</h2>
        <p>Enter your e-mail and we will send you a password recovery link.</p>
        <form id="forgottenPasswordForm">
            <input type="email" placeholder="Enter email address" name="email" id="email-field" required>
            <button type="button" id="forgot-password-button">Send email</button>
        </form>
    </div>

    <div id="return-message"></div>
    <a href="../index.php">Go back</a>

</main>

<footer>
</footer>

</body>
</html>
