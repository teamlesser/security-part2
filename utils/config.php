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
    "host" => "localhost",
    "port" => 5432,
    "dbname" => "securitylab",
    "user" => "group4lab",
    "password" => "s`yWSqL[Zh6@6u[G",
    "debug" => true,

    // JWT Secret Key (base 64 encoded)
    "JWTSecretKey" => "VGZlTUs4RHhoTnQ1WHkxbVpvYk9EQTZqdnhQeDhVUkxxU3M3bWU5VU5wdTdqRk8zY3JrV3k1UkV4S3NScTlO"
);