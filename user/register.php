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
    <link rel="stylesheet" href="../css/style.css"/>
    <script src="../js/register.js"></script>
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

        <div id="return-message"></div><br>
        <a href="../index.php">Go back</a>
    </div>
</main>

<footer>
    <p><a href="mailto:dawe1103@student.miun.se">Daniel</a> | <a href="mailto:rovi1601@student.miun.se">Robin</a> | <a href="mailto:lobe1602@student.miun.se">Bernard</a> | <a href="mailto:heho1602@student.miun.se">Heidi</a> | <a href="mailto:anli1606@student.miun.se">Andreas</a></p>
</footer>

</body>
</html>

