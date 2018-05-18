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
    <div id="login-div">
        <h1>Security lab</h1>
        <form id="loginForm">
            <input id="email-field" placeholder="E-mail">
            <input id="password-field" placeholder="Password">
        </form>
        <button id="login-button">Login</button>
        <br>
        <a href="user/register.php">New user?</a>
        <br>
        <a href="user/resetpassword.php">Forgot password?</a>
    </div>
    <div id="return-message"></div>
    <br><br><br>

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
    </section>
</main>


<footer>
    <p><a href="mailto:dawe1103@student.miun.se">Daniel</a> | <a href="mailto:rovi1601@student.miun.se">Robin</a> | <a href="mailto:lobe1602@student.miun.se">Bernard</a> | <a href="mailto:heho1602@student.miun.se">Heidi</a> | <a href="mailto:anli1606@student.miun.se">Andreas</a></p>
</footer>
</body>
</html>