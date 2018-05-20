/**
 * File: login.js
 * Desc: File for handling login through Ajax. Sends and receives data from login.php.
 * Date: 2018-05-11
 */

/**
 * Variable for XMLHttpRequest-object
 */
var xhr;

/**
 * Gets a HTML-element by id-name.
 * @param id The id of the page element to return.
 * @returns {HTMLElement | null}
 */
function byId(id) {
    return document.getElementById(id);
}

/**
 * The main function. Adds listener to login button and
 * creates XMLHttpRequest-object.
 */
function main(){

    //Adds EventListeners to buttons.
    byId("login-button").addEventListener('click', doLogin, false);

    // Creates matching XMLHttpRequest-object
    try {
        if (window.XMLHttpRequest) {
            // Supported by newer browsers
            xhr = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            // Supported by IE6, IE5
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            throw new Error('Cannot create XMLHttpRequest object');
        }

    } catch (e) {
        alert('"XMLHttpRequest failed!' + e.message);
    }
}

/**
 * Sends a POST to processLogin.php that will attempt to login an user.
 * Gets values from email and password fields and passes it on to server.
 */
function doLogin() {
    // Checks that fields aren't empty
    if(byId("email-field").value !== "" && byId("password-field").value !== ""){

        // Gets values of username and password for message
        var email = byId("email-field").value;
        var password = byId("password-field").value;

        // Check that e-mail has correct format
        var emailRegex = /[\w]+@[\w]+\.[a-zA-Z]+/;

        if (email.match(emailRegex)){

            // EventListener for server state change
            xhr.addEventListener('readystatechange', processLogin, false);

            // Message is composed and sent as JSON
            var data = JSON.stringify({"email": email, "password": password});

            xhr.open("POST", "user/processLogin.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(data);

        }

        else {
            byId("return-message").innerHTML = "E-mail has incorrect format.";
        }

    }

    else {
        byId("return-message").innerHTML = "At least one of the fields is empty.";
    }
}

/**
 * Processes the received response from the
 * server when attempting to login.
 */
function processLogin(){

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){

        // Removes the EventListener since the xhr-object is reused
        xhr.removeEventListener("readystatechange", processLogin, false);

        // Handles response message
        var response = JSON.parse(this.responseText);

        byId("return-message").innerHTML = response.message;

        if (response.status === "success"){
            window.location.replace("/securitylab/user/main.php");
        }
    }
}

//Main is run once the page has finished loading.
window.addEventListener("load", main, false);