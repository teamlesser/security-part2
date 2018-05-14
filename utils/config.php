<?php
/*******************************************************************************
 * File: config.php
 *
 * Desc: Contains configuration data for connecting to database
 * and other things that will be served by the Config-class.
 *
 * Date: 2018-05-08
 ******************************************************************************/

/**
 * Directly returns array containing configurations.
 * 2018-05-11: The data actual. Please make sure that you
 * have the role group4lab created before using.
 */
return array(
    // Database Connection
    "host" => "localhost",
    "port" => 5432,
    "dbname" => "securitylab",
    "user" => "group4lab",
    "password" => "s`yWSqL[Zh6@6u[G",
    "debug" => true,

    // SMTP Connection
    "SMTPDebug" => 2,                               // SMTP Debugging : 1 = errors and messages, 2 = messages only
    "SMTPAuth" => true,                             // If authentication should be enabled
    "SMTPSecure" => "tls",                          // Secure transfer enabled (REQUIRED for Gmail)
    "SMTPHost" => "smtp.gmail.com",                 // Our SMTP host
    "SMTPPort" => 587,                              // Port for SMTP
    "SMTPIsHtml" => true,                           // Does the mail contain HTML?
    "SMTPUsername" => "<your-gmail>@gmail.com",     // Username to Gmail
    "SMTPPassword" => "<your-password>"             // Password to Gmail

);