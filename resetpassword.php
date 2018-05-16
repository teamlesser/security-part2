<!DOCTYPE html>
<html lang="en-EN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
    <script src="js/resetpassword.js"></script> 
</head>
<body>
	
	<div id="resetPassword">
        <h2>Forgot Password</h2>
        <form id="resetPasswordForm">
            <label for="email">Email:</label><br/>
            <input type="email" placeholder="Enter your email..." name="email" id="email" required><br/>
			<label for="resettoken">Reset token:</label><br/>
			<input type="text" placeholder="Enter reset token" name="resettoken" id="resettoken" required><br/>
			<label for="newpass1">New password:</label><br/>
            <input type="password" placeholder="Enter new password" name="newpass1" id="newpass1" required><br/>
			<label for="newpass2">Confirm new password:</label><br/>
            <input type="password" placeholder="Confirm new password" name="newpass2" id="newpass2" required><br/>
            <button type="button" id="resetPassword-button">Reset Password</button>
        </form>
    </div>
	
	<p id="result"></p>

</body>
</html>
