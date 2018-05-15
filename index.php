<?PHP
/**
+ * File: index.php

+ * Date: 2018-05-14

+ * Desc: The start page where a user can login, navigate to register.php and resetpassword.php

+ */

$title = "Security Lab - Group Four";
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

        <h1>Login</h1>
        <div id="login-div">
            <input id="email-field" placeholder="E-mail" type="email">
            <input id="password-field" placeholder="Password" type="password">
            <br>
            <button id="login-button">Login</button>
            <br>
            <a href="register.php">New user?</a>
            <br>
            <a href="resetpassword.php">Forgot password?</a>
        </div>


        <div id="return-message"></div>

    </main>

    <footer>

    </footer>
    </body>
</html>