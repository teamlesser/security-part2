<?PHP
/**
 * + * File: index.php
 *
 * + * Date: 2018-05-14
 *
 * + * Desc: The start page where a user can login, navigate to register.php and resetpassword.php
 *
 * + */
$title = "Security Lab - Group Four | Home page";
?>

<!--

**** HTML-SECTION STARTS HERE ****

-->

<!DOCTYPE html>
<html lang="sv-SE">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DT167G - <?php echo $title ?></title>
    <link rel="stylesheet" href="css/style.css"/>
    <script src="js/login.js"></script>
</head>

<body>
<header>

</header>

<main>
    <aside id="verification-section">
        <h1>Login</h1>
        <div id="login-div">
            <form id="loginForm">
                <label for="email-field">Email:
                    <input id="email-field" placeholder="E-mail">
                </label>
                <label for="password-field">Password:
                    <input id="password-field" placeholder="Password">
                </label>
            </form>
            <button id="login-button">Login</button>
            <br>
            <a href="user/register.php">New user?</a>
            <br>
            <a href="user/resetpassword.php">Forgot password?</a>
        </div>
        <div id="return-message"></div>
    </aside>
    <section id="main-section">
        <div id="intro">
            <h3>Welcome the the Twitter clone of Andreas, Daniel, Heidi, Bernard &amp; Robin</h3>
            <p>This is a small project for our course Software Security. The aim of this small project is to make a site
                as secured as possible. By security, we mean a site that cannot be compromised easily. </p>
            <h4>Things you can do</h4>
            <p>As a <b>user</b>, you will be able to</p>
            <ul>
                <li><a href="user/register.php">Register</a> a new account</li>
                <li><a href="user/login.php">Login </a> to an existing account</li>
                <li>Post a message</li>
                <li>Up/Down-vote message(s)</li>
            </ul>
            <p>As a <b>hacker</b>, your task is to compromise this site in whatever way you can. Good luck with that!
            </p>
        </div>
    </section>
</main>

<footer>

</footer>
</body>
</html>